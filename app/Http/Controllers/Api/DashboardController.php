<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\News;
use App\Models\Gallery;
use App\Models\ContactMessage;
use App\Models\Content;
use App\Models\Tentang;
use Illuminate\Http\Request;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function statistics(Request $request)
    {
        $period = $request->get('period', 'month');
        
        $stats = [
            'total_news' => News::where('is_published', true)->count(),
            'total_galleries' => Gallery::count(),
            'total_messages' => ContactMessage::count(),
            'total_contents' => Content::count(),
        ];
        
        $chartData = $this->getChartData($period);
        
        return response()->json([
            'success' => true,
            'data' => [
                'stats' => $stats,
                'chart_data' => $chartData,
            ]
        ]);
    }

    private function getChartData($period)
    {
        $now = Carbon::now();
        
        switch ($period) {
            case 'today':
                return $this->getTodayData($now);
            case 'three_days':
                return $this->getThreeDaysData($now);
            case 'week':
                return $this->getWeeklyData($now);
            case 'month':
            default:
                return $this->getMonthlyData($now);
        }
    }

    private function getTodayData($now)
    {
        $dateStr = $now->toDateString();
        
        $newsCount = News::whereDate('created_at', $dateStr)->count();
        $galleryCount = Gallery::whereDate('created_at', $dateStr)->count();
        $contentCount = Content::whereDate('created_at', $dateStr)->count();
        $tentangCount = Tentang::whereDate('created_at', $dateStr)->count();
        
        return [[
            'label' => 'Hari Ini',
            'news' => $newsCount,
            'gallery' => $galleryCount,
            'content' => $contentCount,
            'tentang' => $tentangCount,
            'total' => $newsCount + $galleryCount + $contentCount + $tentangCount,
        ]];
    }

    private function getThreeDaysData($now)
    {
        $data = [];
        for ($i = 2; $i >= 0; $i--) {
            $date = $now->copy()->subDays($i);
            $dateStr = $date->toDateString();
            
            $newsCount = News::whereDate('created_at', $dateStr)->count();
            $galleryCount = Gallery::whereDate('created_at', $dateStr)->count();
            $contentCount = Content::whereDate('created_at', $dateStr)->count();
            $tentangCount = Tentang::whereDate('created_at', $dateStr)->count();
            
            $data[] = [
                'label' => $date->format('D d/m'),
                'news' => $newsCount,
                'gallery' => $galleryCount,
                'content' => $contentCount,
                'tentang' => $tentangCount,
                'total' => $newsCount + $galleryCount + $contentCount + $tentangCount,
            ];
        }
        return $data;
    }

    private function getWeeklyData($now)
    {
        $data = [];
        for ($i = 6; $i >= 0; $i--) {
            $date = $now->copy()->subDays($i);
            $dateStr = $date->toDateString();
            
            $newsCount = News::whereDate('created_at', $dateStr)->count();
            $galleryCount = Gallery::whereDate('created_at', $dateStr)->count();
            $contentCount = Content::whereDate('created_at', $dateStr)->count();
            $tentangCount = Tentang::whereDate('created_at', $dateStr)->count();
            
            $data[] = [
                'label' => $date->format('D'),
                'news' => $newsCount,
                'gallery' => $galleryCount,
                'content' => $contentCount,
                'tentang' => $tentangCount,
                'total' => $newsCount + $galleryCount + $contentCount + $tentangCount,
            ];
        }
        return $data;
    }

    private function getMonthlyData($now)
    {
        $data = [];
        for ($i = 29; $i >= 0; $i--) {
            $date = $now->copy()->subDays($i);
            $dateStr = $date->toDateString();
            
            $newsCount = News::whereDate('created_at', $dateStr)->count();
            $galleryCount = Gallery::whereDate('created_at', $dateStr)->count();
            $contentCount = Content::whereDate('created_at', $dateStr)->count();
            $tentangCount = Tentang::whereDate('created_at', $dateStr)->count();
            
            $data[] = [
                'label' => $date->format('d/m'),
                'news' => $newsCount,
                'gallery' => $galleryCount,
                'content' => $contentCount,
                'tentang' => $tentangCount,
                'total' => $newsCount + $galleryCount + $contentCount + $tentangCount,
            ];
        }
        return $data;
    }
}
