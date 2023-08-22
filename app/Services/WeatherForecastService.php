<?php

namespace App\Services;

use App\Exceptions\OutsideApiException;
use App\Models\Weather;
use Illuminate\Support\Facades\Http;

class WeatherForecastService
{
    private $url = 'api.openweathermap.org/data/2.5/forecast?q=Moscow&cnt=7&appid=4783d428fcc58b4fb8ca4cd55d692941&units=metric&lang=ru';

    public function get()
    {
        $result = Weather::all()->toArray();
        if (empty($result)) {
            $this->getFromOutsideAPI();
            $result = Weather::all()->toArray();
        }
        return $result;
    }

    private function arrTransformation(array $arr)
    {
        $result = [];
        foreach ($arr as $item) {
            $result[] = [
                'date'=> date("Y-m-d",strtotime(($item->dt_txt))),
                'temperature' => round($item->main->temp),
                'cloudiness' => round($item->clouds->all, -1),
            ];
        }
        return $result;
    }

    private function getFromOutsideAPI()
    {
        $response = Http::get($this->url);
        if ($response->status() === 200) {
            $weather = json_decode($response->getBody()->getContents());
            $this->store($weather->list);
        } else {
            throw new OutsideApiException('server error');
        }
    }

    private function store(array $weather)
    {
        Weather::insert($this->arrTransformation($weather));
    }
}
