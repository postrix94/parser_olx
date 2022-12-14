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
            'title' => "π {$this->title}",  
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
        $delimetr = "\nβββββββββββββ\n";
        $content = [];

        $body = ['tag'=> 'div', 'children' => [$this->body . "\n\n"]];
        $price = ['tag' => 'b', 'children'=>["β Π¦Π΅Π½Π° {$this->price} + ΠΊΠΎΠΌΠΈΡΡΠΈΡ\n"]];
        $informationApartment = ['tag'=>'b', 'children'=>[$delimetr . implode(" | ",$this->informationApartment) . $delimetr]];
        $userContact = ['tag' => 'b', 'children'=> ["π² ΠΠ»Ρ ΡΠ²ΡΠ·ΠΈ {$this->user['phone']} - {$this->user['name']} (ΠΠ°ΠΉΠ±Π΅Ρ, WatsApp)."]];
        $writeMeText = ['tag' => 'a', 'children' => ["\nΠΠ°ΠΏΠΈΡΠ°ΡΡ ΠΌΠ½Π΅ Π² Π’Π΅Π»Π΅Π³ΡΠ°ΠΌ (Π½Π°ΠΆΠ°ΡΡ)"],'attrs' => ['href' => $this->user['personal_link_telegram']]];
        $telegramChannelLink = ['tag' => 'a', 'children' => ["\n\nβ Π’Π΅Π»Π΅Π³ΡΠ°ΠΌ ΠΊΠ°Π½Π°Π» Ρ Π΄ΡΡΠ³ΠΈΠΌΠΈ ΠΊΠ²Π°ΡΡΠΈΡΠ°ΠΌΠΈ"], 'attrs'=> ['href' => $this->user['author_url']]];
        
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