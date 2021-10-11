<?php

use TelegramBot\Api\BotApi;
use TelegramBot\Api\Client;
use TelegramBot\Api\Types\Message;

/**
 * Description of bot
 *
 * @author семья
 */
class Bot {
    private $bot;
    private $config;
    
    public function __construct($config){
        $this->config = $config;
        $this->bot = new Client($this->config['token']);
        $this->model = new Model($this->config['db']);
    }
    
    public function setCommands($config, $config_account, $config_btn){ 
        // регистрируем команду start и указываем для нее callback
        $bot = $this->bot;
        $model = $this->model;
        
        $bot->command('start', function ($message) use ($bot, $config, $model) {
            /** @var Message $message */
            try {

                if($model->is_exists($message->getChat()->getId()) 
                    || $model->registration($message->getChat()->getUsername(), $message->getChat()->getFirstName(), $message->getChat()->getLastName(), $message->getChat()->getId()))
                {
                    $ans = $config['home']['ans'];
                    $keyboard = new \TelegramBot\Api\Types\ReplyKeyboardMarkup($config['home']['btn'], false, true);
                    $bot->sendMessage($message->getChat()->getId(), $ans, false, null,null,$keyboard);
                }
                else
                {
                    $bot->sendMessage($message->getChat()->getId(), "Вы не авторизованы в боте!");
                }
            } catch (\TelegramBot\Api\Exception $e) {
                file_put_contents('error.log', $e->getMessage());
            }
        });

        $bot->on(
            function($update) use ($bot, $config, $config_account, $config_btn, $model){
                $callback = $update->getCallbackQuery();
                $data = '';
                if (!is_null($callback) && strlen($callback->getData()))
                {
                    $message = $callback->getMessage();
                    $data = $callback->getData();
                }
                else
                {
                    $message = $update->getMessage();
                }
                $mtext = $message->getText();
                $chatId = $message->getChat()->getId();

                if(!$model->is_city_user($chatId))
                {
                    if($model->is_country($data) == 0 && $model->is_city($data) == 0)
                    {
                        $res = $model->getCountrys();
                        $country = [];
                        foreach ($res as $val)
                        {
                            $country[] = [['callback_data' => $val['name'], 'text' => $val['description']]];
                        }
                        $keyboard = new \TelegramBot\Api\Types\Inline\InlineKeyboardMarkup($country);
                        $bot->sendMessage($chatId, "⬇️Выберите страну ⬇️", false, null,null,$keyboard);
                    }
                    elseif($model->is_country($data) == 1)
                    {
                        $res = $model->getCitysForCountry($data);
                        $city = [];
                        foreach ($res as $val)
                        {
                            $city[] = [['callback_data' => $val['name'], 'text' => $val['description']]];
                        }
                        $keyboard = new \TelegramBot\Api\Types\Inline\InlineKeyboardMarkup($city);
                        $bot->sendMessage($chatId, "⬇️Выберите город ⬇️", false, null,null,$keyboard);
                    }
                    elseif($model->is_city($data) == 1)
                    {
                        $city_id = $model->getCityForName($data);
                        if($city_id != 0)
                        {
                            $model->setCityUser($chatId, $city_id);
                            $bot->sendMessage($chatId, "⬇️Ваш профиль сохранен ⬇️");
                        }
                        else
                        {
                            $bot->sendMessage($chatId, "⬇️Ваш профиль не сохранен ⬇️");
                        }
                    }
                    else
                    {
                        $bot->sendMessage($chatId, "⬇️Ваш профиль не сохранен 1 ⬇️");
                    }
                    if (!is_null($callback) && strlen($callback->getData()))
                    {
                        $bot->answerCallbackQuery($callback->getId());
                    }
                }
                elseif(isset($config_btn['btn']['marka'][$data]))
                {                   
                    $keyboard = new \TelegramBot\Api\Types\Inline\InlineKeyboardMarkup(
                        $config_btn['btn']['marka'][$data]
                    );
                    $bot->sendMessage($chatId, "⬇️Выберите год ⬇️", false, null,null,$keyboard);
                    $bot->answerCallbackQuery($callback->getId());
                }
                elseif(isset($config_btn['btn']['model'][$data]))
                {                   
                    $keyboard = new \TelegramBot\Api\Types\Inline\InlineKeyboardMarkup(
                        $config_btn['btn']['model'][$data]
                    );
                    $bot->sendMessage($chatId, "⬇️Выберите модель⬇️", false, null,null,$keyboard);
                    $bot->answerCallbackQuery($callback->getId());
                }
                elseif(isset($config_btn['btn']['info'][$data]))
                {                   
                    $keyboard = new \TelegramBot\Api\Types\Inline\InlineKeyboardMarkup(
                        $config_btn['btn']['info'][$data]
                    );
                    $bot->sendMessage($chatId, "Информация", false, null,null,$keyboard);
                    $bot->answerCallbackQuery($callback->getId());
                }
                elseif(isset($config_btn['btn']['warning_diagram'][$data])){
                    $keyboard = new \TelegramBot\Api\Types\Inline\InlineKeyboardMarkup(
                        $config_btn['btn']['warning_diagram'][$data]
                    );
                    $bot->sendMessage($chatId, "⬇️Выберите нужную схему⬇️", false, null,null,$keyboard);
                    $bot->answerCallbackQuery($callback->getId());
                }
                elseif(isset($config_btn['btn']['parts'][$data])){
                    $keyboard = new \TelegramBot\Api\Types\Inline\InlineKeyboardMarkup(
                        $config_btn['btn']['parts'][$data]
                    );
                    $bot->sendMessage($chatId, "⬇Выберите Свой Комплект⬇", false, null,null,$keyboard);
                    $bot->answerCallbackQuery($callback->getId());
                }elseif(isset($config_btn['btn']['parts_kit'][$data])){
                    $keyboard = new \TelegramBot\Api\Types\Inline\InlineKeyboardMarkup(
                        $config_btn['btn']['parts_kit'][$data]
                    );
                    $bot->sendMessage($chatId, "⬇Состав комплекта⬇", false, null,null,$keyboard);
                    $bot->answerCallbackQuery($callback->getId());
                }
                elseif(isset($config_btn['btn']['component_location'][$data])){
                    $keyboard = new \TelegramBot\Api\Types\Inline\InlineKeyboardMarkup(
                        $config_btn['btn']['component_location'][$data]
                    );
                    $bot->sendMessage($chatId, "⬇️Выберите нужную схему⬇️", false, null,null,$keyboard);
                    $bot->answerCallbackQuery($callback->getId());
                }
                elseif(isset($config['btn']['code_fail_type'][$data])){
                    $keyboard = new \TelegramBot\Api\Types\Inline\InlineKeyboardMarkup(
                        $config['btn']['code_fail_type'][$data]
                    );
                    $bot->sendMessage($chatId, "Введите код ошибки", false, null,null,$keyboard);
                    $bot->answerCallbackQuery($callback->getId());
                }
                //страны
                elseif(mb_stripos($mtext, "Europe") !== false)
                {
                    $keyboard = new \TelegramBot\Api\Types\Inline\InlineKeyboardMarkup(
                       $config_btn['btn']['country']['Europe']
                    );
                    $bot->sendMessage($chatId, "Выберите марку авто", false, null,null,$keyboard);
                }
                elseif(mb_stripos($mtext, "Asia") !== false){
                    $keyboard = new \TelegramBot\Api\Types\Inline\InlineKeyboardMarkup(
                        $config_btn['btn']['country']['Asia']
                    );
                    $bot->sendMessage($chatId, "Выберите марку авто", false, null, null, $keyboard);
                }
                elseif(mb_stripos($mtext, "USA") !== false){
                    $keyboard = new \TelegramBot\Api\Types\Inline\InlineKeyboardMarkup(
                        $config_btn['btn']['country']['USA']
                    );
                    $bot->sendMessage($chatId, "Выберите марку авто", false, null, null, $keyboard);
                }
                elseif(mb_stripos($mtext, "China") !== false){
                    $keyboard = new \TelegramBot\Api\Types\Inline\InlineKeyboardMarkup(
                        $config_btn['btn']['country']['China']
                    );
                    $bot->sendMessage($chatId, "Выберите марку авто", false, null, null, $keyboard);
                }
                elseif(mb_stripos($mtext, "🌍Все марки автомобилей🌍") !== false){
                    
                    $keyboard = new \TelegramBot\Api\Types\Inline\InlineKeyboardMarkup(
                        $config_btn['btn']['all_cars']
                    );
                    $bot->sendMessage($chatId, "Выберите марку авто из списка популярных марок", false, null, null, $keyboard);
                }
                elseif(mb_stripos($mtext, "💼 Личный кабинет") !== false){
                    $keyboard = new \TelegramBot\Api\Types\ReplyKeyboardMarkup(
                        $config_account['btn'], false, true
                    );
                    $bot->sendMessage($chatId, "Измените личные данные!", false, null, null, $keyboard);
                }
                elseif(mb_stripos($mtext, "👤 Имя") !== false){
                    $bot->sendMessage($chatId, "Введите Имя через доллар $");
                }
                elseif(mb_stripos($mtext, "$") !== false){
                    $keyboard = new \TelegramBot\Api\Types\ReplyKeyboardMarkup(
                        $config_account['btn'], false, true
                    );
                    $bot->sendMessage($chatId, "Ваше имя сохранено ".$mtext, false, null, null, $keyboard);
                }
                elseif(mb_stripos($mtext, "📧 E-mail") !== false){
                    $bot->sendMessage($chatId, "Введите Почта через собачку @");
                }
                elseif(mb_stripos($mtext, "🔝 На главную") !== false){
                    $ans = $config['home']['ans'];
                    $keyboard = new \TelegramBot\Api\Types\ReplyKeyboardMarkup($config['home']['btn'], false, true);
                    $bot->sendMessage($chatId, $ans, false, null,null,$keyboard);
                }
                elseif(mb_stripos($mtext, "@") !== false){
                    $keyboard = new \TelegramBot\Api\Types\ReplyKeyboardMarkup(
                        $config_account['btn'], false, true
                    );
                    $bot->sendMessage($chatId, "Ваше почта сохранена ".$mtext, false, null, null, $keyboard);
                }

                elseif(array_key_exists($mtext,$config['search']['marka']) !== false){
                    $keyboard = new \TelegramBot\Api\Types\Inline\InlineKeyboardMarkup(
                        $config['search']['marka'][$mtext]
                    );
                    $bot->sendMessage($chatId, "Выберите марку авто из списка популярных марок", false, null, null, $keyboard);
                }
                elseif(isset($config_btn['btn']['description'][$data]))
                {   
                    if(isset($config_btn['btn']['description'][$data]['img']))
                    {   
                        foreach ($config_btn['btn']['description'][$data]['img'] as $value) {
                            $img = curl_file_create($value,'image/png'); 
                            $bot->sendPhoto($chatId, $img); 
                        } 
                    } 
                    if(isset($config_btn['btn']['description'][$data]['pdf']))
                    {   
                        foreach ($config_btn['btn']['description'][$data]['pdf'] as $value) {
                            $pdf = curl_file_create($value,'application/pdf'); 
                            $bot->sendDocument($chatId, $pdf, '✅Откройте Ваш файл✅'); 
                        } 
                    } 
                    if(isset($config_btn['btn']['description'][$data]['svg']))
                    {   
                        foreach ($config_btn['btn']['description'][$data]['svg'] as $value) {
                            $svg = curl_file_create($value,'image/svg+xml'); 
                            $bot->sendDocument($chatId, $svg, '✅Откройте Ваш файл✅'); 
                        } 
                    } 
                    if(isset($config_btn['btn']['description'][$data]['html']))
                    {
                        $bot->sendMessage($chatId, $config_btn['btn']['description'][$data]['html'], 'HTML');
                    }
                    $bot->answerCallbackQuery($callback->getId());    
                }
            },  
            function($message) use ($bot){
                return true; // когда тут true - команда проходит
            }
        );

    }
    
    public function run(){
        try {
            // запускаем обработку полученных данных
            $this->bot->run();
        } catch (\TelegramBot\Api\Exception $e) {
            file_put_contents('error.log', $e->getMessage());
        }
    }
}