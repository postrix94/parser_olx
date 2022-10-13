<?php
namespace app\services;

use app\bootstrap\views\Views;
use GuzzleHttp\Client;
use app\model\Auth;
use PDOException;

class Telegraph {
    private const LINK_CREATE_POST = 'https://api.telegra.ph/createPage';
    private $originalLink = '';
    private $title = "";
    private $price = '';
    private $body = '';
    private $informationApartment = [];
    private $images = [];
    private $user = null;


    public function __construct(array $informationApartment,$cookie)
    {
        $auth = new Auth();
        
        $this->originalLink = $informationApartment['link'];
        $this->title = $informationApartment['title'];
        $this->price = $informationApartment['price'];
        $this->body = $informationApartment['text'];
        $this->informationApartment = $informationApartment['informationApartment'];
        $this->imgs = $informationApartment['images'];
        $this->user = $auth->getUserWithCookie($cookie);
    }

    public function addPost() {
        $client = new Client();

        try{
            $response = $client->post(self::LINK_CREATE_POST, 
            ['json'=> 
            ['access_token' => $this->user['telegraph_token'], 
            'author_url' => $this->user['author_url'],
            'author_name' => $this->user['author_name'],
            'title' => "📌 {$this->title}",  
            'content' => $this->getContentFormatTelegraph()
            ]],
        );
        }catch(PDOException $e) {
            die($e->getMessage());
        }

        $res = json_decode($response->getBody()->getContents());
        if(!$res->ok) {
            Views::getHomePage(message:$res->error); 
            die;
        }

        return $res->result->url;
    }

    private function getContentFormatTelegraph() {
        $delimetr = "\n➖➖➖➖➖➖➖➖➖➖➖➖➖\n";
        $content = [];

        $body = ['tag'=> 'div', 'children' => [$this->body . "\n\n"]];
        $price = ['tag' => 'b', 'children'=>["✅ Цена {$this->price} + комиссия\n"]];
        $informationApartment = ['tag'=>'b', 'children'=>[$delimetr . implode(" | ",$this->informationApartment) . $delimetr]];
        $userContact = ['tag' => 'b', 'children'=> ["📲 Для связи {$this->user['phone']} - {$this->user['name']} (Вайбер, WatsApp)."]];
        $writeMeText = ['tag' => 'a', 'children' => ["\nНаписать мне в Телеграм (нажать)"],'attrs' => ['href' => $this->user['personal_link_telegram']]];
        $telegramChannelLink = ['tag' => 'a', 'children' => ["\n\n✅ Телеграм канал с другими квартирами"], 'attrs'=> ['href' => $this->user['author_url']]];
        
        array_push($content,$body,$price,$informationApartment, 
        $userContact, $writeMeText,$telegramChannelLink,
        ...$this->getImagesFormatTelegtaph(),);

        return $content;
    }

    private function getImagesFormatTelegtaph():array {
        $images = [];
        if(!count($this->imgs)) return $images;

        foreach($this->imgs as $img) {
            array_push($images, ['tag'=>'img','attrs'=>['src'=>$img]]);
        }

        return $images;
    }
}