<?php

namespace App\Http\Controllers;


use App\Models\Article;
use App\Models\Contact;
use App\Models\Product;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Cache;

class MainController extends Controller
{
    public function index(): JsonResponse
    {

        return response()->json(
            [
                'quotes'   => $this->getQuotes(),
                'products' => $this->getProducts(),
                'articles' => $this->getArticles(),
            ]
        );
    }

    public function contacts()
    {
        $contact = Contact::query()
            ->withTranslation($this->getLocale())
            ->first();

        return response()->json(
            [
                'email'   => $contact->address,
                'address' => $contact->getTranslatedAttribute('address', $this->getLocale()),
                'phone'   => $contact->phone,
            ]
        );
    }

    private function getProducts(): array
    {
        $result   = [];
        $products = Product::query()
            ->where('is_active', true)
            ->withTranslation($this->getLocale())
            ->orderByDesc('order')
            ->get();

        foreach ($products as $product) {
            $result[] = [
                'id'    => $product->id,
                'title' => $product->getTranslatedAttribute('title', $this->getLocale()),
                'image' => asset('storage/' . $product->image),
                'text'  => $product->getTranslatedAttribute('text', $this->getLocale()),
            ];
        }

        return $result;
    }

    private function getArticles(): array
    {
        $result   = [];
        $articles = Article::query()
            ->where('is_active', true)
            ->orderByDesc('created_at')
            ->limit(3)
            ->withTranslation($this->getLocale())
            ->get();

        foreach ($articles as $article) {
            $result[] = [
                'id'         => $article->id,
                'title'      => $article->getTranslatedAttribute('title', $this->getLocale()),
                'created_at' => $article->created_at,
            ];
        }

        return $result;
    }

    private function getQuotes(): array
    {
        $this->getUsd();

        return [
            $this->getUsd(),
            $this->getEur(),
            $this->getRub(),
            $this->getBrent(),
        ];
    }

    private function getUsd(): array
    {
        if (Cache::has('USD')) {
            $data = Cache::get('USD');
        } else {
            $apiUrl   = "https://v6.exchangerate-api.com/v6/3b2544bbd615acc901fee7af/latest/USD";
            $response = file_get_contents($apiUrl);

            $data = [
                'name'  => 'USD',
                'cost'  => json_decode($response, true)['conversion_rates']['KZT'],
                'is_up' => true,
            ];

            if (Cache::has('USD_OLD')) {
                $data['is_up'] = $data['cost'] >= Cache::get('USD_OLD');
            }

            Cache::add('USD_OLD', $data['cost'], (now())->addHours(3));
            Cache::add('USD', $data, (now())->addHours(2));
        }
        $data['cost'] = round($data['cost'], 2);

        return $data;
    }

    private function getEur(): array
    {
        if (Cache::has('EUR')) {
            $data = Cache::get('EUR');
        } else {
            $apiUrl   = "https://v6.exchangerate-api.com/v6/3b2544bbd615acc901fee7af/latest/EUR";
            $response = file_get_contents($apiUrl);

            $data = [
                'name'  => 'EUR',
                'cost'  => json_decode($response, true)['conversion_rates']['KZT'],
                'is_up' => true,
            ];

            if (Cache::has('EUR_OLD')) {
                $data['is_up'] = $data['cost'] >= Cache::get('EUR_OLD');
            }

            Cache::add('EUR_OLD', $data['cost'], (now())->addHours(3));
            Cache::add('EUR', $data, (now())->addHours(2));
        }
        $data['cost'] = round($data['cost'], 2);

        return $data;
    }

    private function getRub(): array
    {
        if (Cache::has('RUB')) {
            $data = Cache::get('RUB');
        } else {
            $apiUrl   = "https://v6.exchangerate-api.com/v6/3b2544bbd615acc901fee7af/latest/RUB";
            $response = file_get_contents($apiUrl);

            $data = [
                'name'  => 'RUB',
                'cost'  => json_decode($response, true)['conversion_rates']['KZT'],
                'is_up' => true,
            ];

            if (Cache::has('RUB_OLD')) {
                $data['is_up'] = $data['cost'] >= Cache::get('RUB_OLD');
            }

            Cache::add('RUB_OLD', $data['cost'], (now())->addHours(3));
            Cache::add('RUB', $data, (now())->addHours(2));
        }

        $data['cost'] = round($data['cost'], 2);

        return $data;
    }

    private function getBrent(): array
    {
        return [
            'name'  => 'BRENT',
            'cost'  => round(77.23, 2),
            'is_up' => true,
        ];
    }
}