<?php
namespace app\services;
use app\bootstrap\views\Views;
use app\model\Auth;

use Goutte\Client;
use Symfony\Component\DomCrawler\Crawler;
use Symfony\Component\HttpClient\HttpClient;
// use Symfony\Contracts\HttpClient\HttpClientInterface;
use Exception;
use PDOException;

class olxParser {
    private $savePhoto = null;
    private $link = null;
    private $userCookie = null;
    const VALIDATION_RULES_IS_OLX_LINK = "*^(https:\/\/)(m|www).olx.ua*";
    const VALIDATION_INFORMATION_APARTMENT = '*(?)(\W|^)(загальна площа|общая площадь|поверх|этаж|кількість кімнат|количество комнат)(\W|$)*';
    const SEARCH_TITLE = 'h1.css-r9zjja-Text.eu5v0x0[data-cy="ad_title"]';
    const SEARCH_PRICE = 'div[data-testid="ad-price-container"]>h3.css-okktvh-Text.eu5v0x0';
    const SEARCH_BODY_TEXT = 'div.css-1m8mzwg[data-cy="ad_description"] > div.css-g5mtbi-Text';
    const SEARTCH_UL_INFORMATION_APARTMENT = 'ul.css-sfcl1s>li';
    const SEARCH_IMAGES = 'img[data-testid="swiper-image-lazy"]';
    const SEARCH_FIRST_IMAGE = 'img[data-testid="swiper-image"]';
    const SEARCH_ID_AD = 'span.css-9xy3gn-Text';
    const SEARCH_NAME_USER = 'h4.css-1rbjef7-Text';

    public function __construct($link,$cookie, $savePhoto = false)
    {
        $this->link = $this->removeQueryParameters($link);
        $this->userCookie = $cookie;
        if($savePhoto) {
            $this->savePhoto = true;
        }
    }
    
  
    public function validationInput() {
        return preg_match(self::VALIDATION_RULES_IS_OLX_LINK, $this->link);
    }

    public function parsingStart() {
        $client = HttpClient::create();

        $headers = [
            'authority' => 'www.olx.ua',
            'scheme' => 'https',
            'accept' => 'text/html,application/xhtml+xml,application/xml;q=0.9,image/avif,image/webp,image/apng,*/*;q=0.8,application/signed-exchange;v=b3;q=0.9',
            'accept-language' => 'ru,en-US;q=0.9,en-GB;q=0.8,en;q=0.7,uk;q=0.6,de-DE;q=0.5,de-LI;q=0.4,de;q=0.3,ru-RU;q=0.2,de-CH;q=0.1,en-GB-oxendict;q=0.1',
            'cache-control' => 'max-age=0',
            'cookie' => 'lang=uk',
            'referer' => 'https://www.olx.ua/',
            'sec-ch-ua-mobile' => '?0',
            'sec-fetch-dest' => 'document',
            'sec-fetch-mode' => 'navigate',
            'sec-fetch-site' => 'same-origin',
            'sec-fetch-user' => '?1',
            'user-agent' => $this->getUserAgent(), 
        ];

        try{
            $response = $client->request('GET', $this->link, ['headers' => $headers]);
            if($response->getStatusCode() == 200) {
                $crawler = new Crawler($response->getContent());
            }else {
                dd($response->getStatusCode());
            }

        }catch(Exception $e) {
            return Views::getHomePage(message:$e->getMessage());
        }

        $informationApartment = $this->getInformationApartment($crawler);
        $user = new Auth();
        $tokenOlx = $user->getOlxToken( $_COOKIE['user'] ?? null);
        $userPhone = $this->getUserPhone($tokenOlx,$informationApartment['idAd']);
        $telegraph = new Telegraph($informationApartment,$this->userCookie);
        $urlTelegraph = $telegraph->addPost();
        $informationApartment['linkTelegraph'] = $urlTelegraph;
        $informationApartment['userPhone'] = $userPhone;
        return $informationApartment;
    }

    private function getUserPhone($token, $idAd):array {
        $client = HttpClient::create(['timeout' => 60]);
        $url = "https://www.olx.ua/api/v1/offers/{$idAd}/limited-phones/";
        $headers = [
            'User-Agent'=> $this->getUserAgent(), 
            'Authorization'=> "Bearer f5a3ec4600c074da57c41e53d4569895937e73a5",
            "cookie" => 'cookie: deviceGUID=bdd1e5eb-bd35-4c97-885f-3229c0975a40; sbjs_migrations=1418474375998%3D1; sbjs_current_add=fd%3D2022-10-10%2010%3A14%3A53%7C%7C%7Cep%3Dhttps%3A%2F%2Fwww.olx.ua%2Fd%2Fmyaccount%7C%7C%7Crf%3D%28none%29; sbjs_first_add=fd%3D2022-10-10%2010%3A14%3A53%7C%7C%7Cep%3Dhttps%3A%2F%2Fwww.olx.ua%2Fd%2Fmyaccount%7C%7C%7Crf%3D%28none%29; sbjs_current=typ%3Dtypein%7C%7C%7Csrc%3D%28direct%29%7C%7C%7Cmdm%3D%28none%29%7C%7C%7Ccmp%3D%28none%29%7C%7C%7Ccnt%3D%28none%29%7C%7C%7Ctrm%3D%28none%29; sbjs_first=typ%3Dtypein%7C%7C%7Csrc%3D%28direct%29%7C%7C%7Cmdm%3D%28none%29%7C%7C%7Ccmp%3D%28none%29%7C%7C%7Ccnt%3D%28none%29%7C%7C%7Ctrm%3D%28none%29; sbjs_udata=vst%3D1%7C%7C%7Cuip%3D%28none%29%7C%7C%7Cuag%3DMozilla%2F5.0%20%28Macintosh%3B%20Intel%20Mac%20OS%20X%2010_15_7%29%20AppleWebKit%2F537.36%20%28KHTML%2C%20like%20Gecko%29%20Chrome%2F96.0.4664.45%20Safari%2F537.36; newrelic_cdn_name=CF; a_access_token=ab226a3a7f5bbbaf7eb8074e3e825c22928bc7d4; a_refresh_token=8dc7f7b08520f62e11ca234594efd2918810a22a; a_grant_type=device; laquesis=buy-1848@a#buy-2893@b#buy-2895@b#decision-206@b#decision-377@b#decision-536@a#decision-790@a#deluareb-1677@a#er-1778@b#euonb-493@a#f8nrp-1218@c#jobs-3717@c#jobs-3837@c#jobs-3845@a#jobs-4145@d#jobs-4259@c#oesx-1547@a#oesx-2020@a; laquesisff=aut-716#buy-2811#decision-657#euonb-114#euonb-48#grw-124#kuna-307#oesx-1437#oesx-1643#oesx-645#oesx-867#olxeu-29763#srt-1289#srt-1346#srt-1434#srt-1593#srt-1758#srt-477#srt-479#srt-682; __gfp_64b=BwuAEMgKOyL1XIZ6YIfPfP0keLe9_X5IfKkSdi29gcX.h7|1665396892; _hjFirstSeen=1; _hjIncludedInSessionSample=0; _hjSession_2218922=eyJpZCI6IjFlMGVkMDNjLWUxNDAtNGYzMC04MzY4LWNlNDc3YzU3YzRjOCIsImNyZWF0ZWQiOjE2NjUzOTY4OTY5MjIsImluU2FtcGxlIjpmYWxzZX0=; _hjAbsoluteSessionInProgress=0; mobile_default=desktop; ldTd=true; _gid=GA1.2.1298021492.1665396901; fingerprint=MTI1NzY4MzI5MTsxNjswOzA7MDsxOzA7MDswOzA7MDsxOzE7MTsxOzE7MTsxOzE7MTsxOzE7MTsxOzE7MDsxOzE7MTswOzA7MDswOzE7MDswOzE7MTsxOzE7MTswOzE7MDswOzE7MTsxOzA7MDswOzA7MDswOzE7MDsxOzA7MDswOzA7MDsxOzA7MTsxOzE7MTsxOzE7MTswOzE7MDszNjI5NzE3ODUyOzI7MjsyOzI7MjsyOzU7Mjg0ODAwNjQxODsxMzU3MDQxNzM4OzE7MTsxOzE7MTsxOzE7MTsxOzE7MTsxOzE7MTsxOzE7MTswOzA7MDszMTU0NjgzMjEzOzM0NjkzMDY1NTE7MTk5OTI5MzY1MDszMzA4Mzg4NDE7Mzk1NTQ0ODY5MzszODQwOzIxNjA7MzA7MzA7MDswOzA7MDswOzA7MDswOzA7MDswOzA7MDswOzA7MDswOzA7MDswOzA7MDsw; dfp_user_id=da713725-46e7-4441-a846-93ad6414be17-ver2; from_detail=0; __gads=ID=72bc2e2ec977e7f1:T=1665396899:S=ALNI_MYvvz9-f4j9xhPZxzvluQjmiWqL3A; __gpi=UID=00000b6b61b6d44e:T=1665396899:RT=1665396899:S=ALNI_MZ4mRB-65bZnKSWx5GsCrNCFk2EfQ; _hjSessionUser_2218922=eyJpZCI6Ijc4ZGZjZDQ0LTVlM2ItNTJlOC05ZjU0LTEwNDg0OGE1M2JhOSIsImNyZWF0ZWQiOjE2NjUzOTY4OTU1NzYsImV4aXN0aW5nIjp0cnVlfQ==; user_adblock_status=false; __utma=250720985.1222558786.1665396901.1665396906.1665396906.1; __utmc=250720985; __utmz=250720985.1665396906.1.1.utmcsr=(direct)|utmccn=(direct)|utmcmd=(none); __utmt=1; PHPSESSID=f52ggejfn19calcdeibeufi3e6; x-device-id=t2z0h0ferrcnjn%3A1665396910942; user_id=842910920; lang=ru; access_token=f5a3ec4600c074da57c41e53d4569895937e73a5; refresh_token=07de18cb9e32a943f9ad5683a1b51efd132637b1; user_business_status=normal; dfp_segment=%5B%5D; __diug=true; _gat=1; security_modal_dismiss=true; lister_lifecycle=1665396935; cookieBarSeen=true; __utmb=250720985.2.10.1665396906; cto_bundle=WwpHv184bjNPYkhhbnZrJTJCOVdwSnFEbTBzejNGbE9kdzIwblRSbXdOa3ptZmo5VjllJTJCTE5WS1p4VEklMkZwVTIyOXJsSUtrRFBlc1UlMkI0TlJQQ2h4b2VTbXRNREVBRmtoYXcyeldKQSUyQjRoWXU3NU5zJTJCRTB0ek9hMURkWHRIb0t5RSUyQlZhM3ZmRHN4eEdaYSUyRiUyRjNZRVJCQU9oQ1YwSCUyQnUlMkZSa2xqaHpRemtJejRJViUyQms1JTJGMDZlTVY0cDAxTVpDRHpqSkVBNHdLNw; sbjs_session=pgs%3D4%7C%7C%7Ccpg%3Dhttps%3A%2F%2Fwww.olx.ua%2Fd%2Fobyavlenie%2Fvorkaut-turnik-shvedskaya-stenka-brusya-dlya-ulitsy-ulichnyy-rukohod-IDHnaWb.html; lqstatus=1665398092|183c163df61x4d2bcd9b|decision-206#buy-2895#buy-2893||; delivery_l1=undefined; _gcl_au=1.1.474863372.1665396959; _ga=GA1.1.1222558786.1665396901; session_start_date=1665398769388; onap=183c163df61x4d2bcd9b-1-183c163df61x4d2bcd9b-16-1665398770; _ga_QFCVKCHXET=GS1.1.1665396852.15.1.1665396969.13.0.0; _gat_clientNinja=1',
            'friction-token' => 'eyJhbGciOiJSUzI1NiIsImtpZCI6Ijg0Yjk0NmZlLWEzNDEtNDFkZi04YTA3LTM0NjdhZmJjMjg4NiIsInR5cCI6IkpXVCJ9.eyJhdWQiOiJhdGxhcyIsImFjdGlvbiI6InJldmVhbF9waG9uZV9udW1iZXIiLCJhY3RvciI6eyJ1c2VybmFtZSI6ImEzYTVlYTk1LTE5YjEtNDhjYS1iNWQ4LTk3ZjIzZWQ3M2Y2NSIsImRlc2lyZWRfZW1haWwiOiIiLCJpcCI6IjkxLjIzMS41NC4yMTgiLCJpcF9jb3VudHJ5IjoiVUEifSwic2NlbmUiOnsib3JpZ2luIjoid3d3Lm9seC51YSIsImFkX2lkIjoiNjQwOTA1OTc3In0sImlzcyI6Imh0dHBzOi8vZnJpY3Rpb24ub2x4Z3JvdXAuY29tIiwiaWF0IjoxNjY1Mzk2OTY3LCJleHAiOjE2NjUzOTY5ODJ9.BBNcVGKgMf3x7bCw_EFAmXM8AIo7nlIof4Zj8AOW7nDl8tfYaNoDM4qzKzfxK6bEfBTzBjopK6JK8VsgKQTgEPZtzaMgAINDENrTu-kn4VCcuy7midg8uNwEgtSBroJBc-K0EST_wd3oYKcCIrRTWmN0OpkFItMFsWlZ1G2-ogEEpicvDRUHdljl-D_H_QQBrX4pNG1B5vevxXL0V01BBNvvtD5P6m1wfI1QZA68YrocW5HN6rgzmARcNAPyEjXR74-bZrSqF9fs1ojIIZNBY5SJGwEzRK8a0LLJhwmhxb1jxh0-rWtgymy1CptxswvrDz8adHMdUVJpq8qmoVPoqg',
        ];

        $response = $client->request('GET', $url, ['headers' =>$headers]);

        if($response->getStatusCode() === 200) {
            $phone = json_decode($response->getContent());
            return $phone->data->phones;
        }
        
        return ['token_error'];
    }

    private function getInformationApartment($crawler) {
        $node = $crawler->filter(self::SEARCH_ID_AD);
        $idAd = $this->getClearIdAd(count($node) ?$node->text() : null);
        $node = $crawler->filter(self::SEARCH_NAME_USER);
        $nameUser = count($node) ? $node->text() : null;
        $node = $crawler->filter(self::SEARCH_TITLE);
        $title = count($node) ? $node->text() : null;
        $node = $crawler->filter(self::SEARCH_PRICE);
        $price = count($node) ? $node->text() : null;
        $node = $crawler->filter(self::SEARCH_BODY_TEXT);
        $body = count($node) ? $node->text() : null;
        $informationApartment = $this->getInformation($crawler->filter(self::SEARTCH_UL_INFORMATION_APARTMENT));
        $firstImg = $this->getLinkImages($crawler->filter(self::SEARCH_FIRST_IMAGE));
        $imgList = $this->getLinkImages($crawler->filter(self::SEARCH_IMAGES));
        array_unshift($imgList, ...$firstImg);
        return ['link' => $this->link,'title'=> $title, 'price' => $price, 'text' => $body, 'informationApartment' => $informationApartment, 'images' => $imgList, 'idAd' => $idAd, 'userName' => $nameUser];
    }

    private function getLinkImages($nodeImgs) {
        return $nodeImgs->each(function($node){
            if($node->attr('data-src')) return $node->attr('data-src');
            if($node->attr('src')) return $node->attr('src');
        });
    }

    private function getInformation($nodeLi):array {
        $informationApartment = [];

        if(!count($nodeLi)) return $informationApartment;

        foreach($nodeLi as $li) {
            $text = $li->textContent;
            if(preg_match(self::VALIDATION_INFORMATION_APARTMENT, mb_strtolower($text))) {
                array_push($informationApartment,$text);
            }
        }

        return $informationApartment;
    }

    private function getClearIdAd($id) {
        if(!$id) return '';
        $res = explode(':', $id);
        if(!isset($res[1])) return "";

        return trim($res[1]);
    }

    private function removeQueryParameters($link) {
        return explode('?', $link)[0];
    }

    private function getUserAgent() {
         $userAgent = [
            'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/100.0.4896.60 Safari/537.36',
            'Mozilla/5.0 (Windows NT 10.0; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/100.0.4896.60 Safari/537.36',
            'Mozilla/5.0 (Windows NT 10.0) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/100.0.4896.60 Safari/537.36',
            'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/100.0.4896.60 YaBrowser/22.3.0 Yowser/2.5 Safari/537.36',
            'Mozilla / 5.0 (Windows NT 10.0; rv: 68.0) Gecko / 20100101 Firefox / 68.0',
            'Mozilla / 5.0 (X11; Ubuntu; Linux x86_64; rv: 75.0) Gecko / 20100101 Firefox / 75.0',
            'Mozilla / 5.0 (Windows NT 6.1; Win64; x64; rv: 74.0) Gecko / 20100101 Firefox / 74.0',
          ];

         $index = array_rand($userAgent);
         return $userAgent[$index];
        }
}