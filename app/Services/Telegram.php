<?php
namespace app\services;
use app\model\Auth;
use Longman\TelegramBot\Telegram as TelegramBot;
// use Longman\TelegramBot\Entities\Update;
use Longman\TelegramBot\Request;


class Telegram {
    private $linkOlx = '';
    private $price = '';
    private $title = '';
    private $text = '';
    private $linkTelegraph = '';
    private $numberOfRooms = '';
    private $informationApartment = '';
    private $images = [];
    private $userPhone = [];
    private $userName = '';
    private $user = null;
    const MAX_LENGTH_TEXT_MESSAGE = 500;
    const MAXIMUM_NUMBER_OF_PHOTO_MESSAGE = 9;

    public function __construct($cookie,$informationApartment = null)
    {
        $auth = new Auth();
        $this->user = $auth->getUserWithCookie($cookie);
        $this->telegram = new TelegramBot($this->user['id_telegram_bot'], $this->user['name_telegram_bot']);


        if($informationApartment) {
            $this->linkOlx = $informationApartment['link'];
            $this->price = $informationApartment['price'];
            $this->title = $informationApartment['title'];
            $this->informationApartment = implode(' | ', $informationApartment['informationApartment']);
            $this->numberOfRooms = $this->getCountRoomsNumber($informationApartment['informationApartment']);
            $this->linkTelegraph = $informationApartment['linkTelegraph'];
            $this->images = $informationApartment['images'];
            $this->userPhone = implode("",$informationApartment['userPhone']);
            $this->userName = $informationApartment['userName'];
            $text = $this->maxLengthDescription($informationApartment['text']);
            $this->text = $this->getTextTelegramFormat($text);
        }
    }

    private function getTextTelegramFormat($text) {
        if(!$text) return '';

        $delimetr = "\n➖➖➖➖➖➖➖➖➖➖➖➖➖\n";

        $textTelegram = "";
        $textTelegram .= "{$this->genereteIdMessage()}\n";
        $textTelegram .= "#аренда #{$this->numberOfRooms}к\n\n";
        // $textTelegram .= $this->title . "\n\n";
        $textTelegram .= $text . "\n\n";
        $textTelegram .= "💳 Цена " . $this->price . "\n";

        if($this->informationApartment) {
            $textTelegram .= $delimetr;
            $textTelegram .= "✅" . $this->informationApartment ;
            $textTelegram .= $delimetr;
        }
      
        $textTelegram .= "Написать мне {$this->user['user_name_telegram']}\n\n";
        $textTelegram .= "📲 {$this->user['phone']}\n\n";
        $textTelegram .= "ДОП.ФОТО⬇️\n{$this->linkTelegraph}";
        return $textTelegram;
    }

    private function genereteIdMessage() {
        return mt_rand(10000,999999);
    }
    public function addMessageTelegramPrivate($message) {
        return Request::sendMessage([
            'chat_id' => $this->user['id_private_chat'],
            'text'=> $message,
            "parse_mode" => "html",

        ]);
    }

    public function addMessageTelegramPublic($images,$content) {
        $media = $this->addDescriptionForMedia($content,$images);
        return Request::sendMediaGroup([
            'chat_id' => $this->user['id_public_chat'],
            'media'=>  [...$media],
        ]);
    }

    private function addDescriptionForMedia($text = null, $images = null):array {
      $images = $this->getImagesTelegramFormat($images);
      if(count($images)) {
        $images[0]['caption'] = $text ? $text : $this->text;
        return $images;
      }

      return [];
    }

    private function maxLengthDescription($text) {
        $text = $text ?? '';

        if(mb_strlen($text) > self::MAX_LENGTH_TEXT_MESSAGE) {
         return mb_strimwidth($text, 0,self::MAX_LENGTH_TEXT_MESSAGE, "...");
        }

        return $text;
    }

    private function getImagesTelegramFormat($images = null):array {
        $allPhoto = [];
        $images = $images ? $images : $this->images;

        foreach($images as $i => $img) {
            if($i > self::MAXIMUM_NUMBER_OF_PHOTO_MESSAGE) break;
            $photo = [];
            $photo['type'] = 'photo';
            $photo['media'] = $img;
            array_push($allPhoto, $photo);
        }

        return $allPhoto;
    }

    public function getMessageTelegramPrivate() {
        return "OLX ПЕРВОИСТОЧНИК\n\n{$this->userName}\n{$this->userPhone}\n\n{$this->linkOlx}\n\n" . $this->text;
    }

    private function getCountRoomsNumber($informationCountRooms) {
        $searchStr ='*(кількість кімнат:(\s\d)|количество комнат:(\s\d))*';
        $string =  implode(" ",$informationCountRooms);
        preg_match($searchStr, mb_strtolower($string), $matches);
        if(!$matches || !isset($matches[2])) return '';
         return trim($matches[2]);
    }

    public function addHashtagToContentTelegram($content,...$hashtags) {
        $results = array();
        array_walk_recursive($hashtags, function ($hashtag) use (&$results){$results[] = $hashtag;});

        $hashtagStr = implode(' ', $results);
        $searchSymbol = "#";
        $pos = strpos($content,$searchSymbol);
        $firstPart = substr($content, 0, $pos);
        $secondPart = substr($content, $pos);
        return $firstPart .  $hashtagStr . " " . $secondPart;
    }

    public function getTextTelegram() {
        return $this->text;
    }

    public function getLinkTelegraph() {
        return $this->linkTelegraph;
    }
}