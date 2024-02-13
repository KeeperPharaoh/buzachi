<?php

namespace App\Http\Controllers;

use App\Models\Article;

class ArticleController extends Controller
{
    public function index()
    {
        $articles = Article::query()
            ->where('is_active', true)
            ->orderByDesc('created_at')
            ->select('id', 'title', 'created_at')
            ->withTranslation($this->getLocale())
            ->paginate();

        foreach ($articles as $article) {
            $article->title = $article->getTranslatedAttribute('title', $this->getLocale());
            unset($article->translations);
        }

        return response()->json(
            [
                'articles' => $articles,
            ]
        );
    }

    public function getById(int $id)
    {
        $article = Article::query()
            ->withTranslation($this->getLocale())
            ->find($id);

        return response()->json(
            [
                'data' => [
                    'id'         => $article->id,
                    'title'      => $article->getTranslatedAttribute('title', $this->getLocale()),
                    'text'       => $article->getTranslatedAttribute('text', $this->getLocale()),
                    'image'      => $article->image ? asset('storage/' . $article->image) : null,
                    'video'      => ($article->video && !empty(json_decode($article->video)))? asset('storage/' . json_decode($article->video)[0]->download_link) : null,
                    'created_at' => $article->created_at,
                ],
            ]
        );
    }
}
