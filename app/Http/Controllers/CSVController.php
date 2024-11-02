<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use League\Csv\Reader;
use League\Csv\Writer;
use Illuminate\Support\Facades\Storage;

class CSVController extends Controller
{
    // CSVファイルのアップロード処理
    public function upload(Request $request)
    {
        $file = $request->file('csv');
    
        if (!$file || !$file->isValid()) {
            return response()->json(['message' => 'Invalid file!'], 400);
        }
    
        // CSVファイルの内容を読み込み
        $path = $file->getRealPath();

        try {
            //csvファイルの文字コードを取得し、その文字コード別にして、ifで場合わけ　例えば、UTF-8 shift-JS

            $content = file_get_contents($path); // ファイルの内容を取得

            // 文字エンコードを自動判定
            $encoding = mb_detect_encoding($content, ['UTF-8', 'SJIS-win', 'EUC-JP', 'ISO-8859-1', 'ASCII']);

            // 判定結果を表示 ↓JSONに反映されるからechoされないようにする
            // echo "Encoding: " . $encoding;

            if($encoding === "SJIS-win"){
                // echo "SJIS=winです";
                // 判定結果を表示 ↓JSONに反映されるからechoされないようにする
                $content = mb_convert_encoding($content, 'UTF-8', 'SJIS-win');
            }
            // $content = mb_convert_encoding($content, 'UTF-8', 'SJIS-win'); // Shift-JISを指定
            $stream = fopen('php://temp', 'r+');
            fwrite($stream, $content);
            rewind($stream);
    
            // Readerを作成
            $csv = Reader::createFromStream($stream);
            $csv->setHeaderOffset(0); // ヘッダーを使用
            $records = $csv->getRecords(); // レコードを取得
    
            $data = []; // レコードを格納する配列

            // ここでデータ処理を行い、保存・操作などを実施
            foreach ($records as $record) {
                // 各レコードのデータを処理
                $data[] = $record; // 各レコードを配列に追加
            }
    
            return response()->json(['message' => "CSV uploaded and processed successfully!文字コードは、$encoding", 'data' => $data], 200, [], JSON_UNESCAPED_UNICODE);

        } catch (\Exception $e) {
            // エラーが発生した場合
            return response()->json(['message' => 'Error processing CSV: ' . $e->getMessage()], 500);
        }
    }
    

    // CSVファイルのエクスポート処理
    public function export()
    {
        // ダミーデータを作成（本来はデータベースから取得）
        $data = [
            ['Name', 'Email', 'Phone'],
            ['John Doe', 'john@example.com', '1234567890'],
            ['Jane Doe', 'jane@example.com', '0987654321']
        ];

        // CSVファイル作成
        $csv = Writer::createFromString('');
        $csv->insertAll($data);

        // ファイルをダウンロードさせる
        $filename = 'exported_data.csv';
        $csvContent = $csv->toString();
        Storage::put($filename, $csvContent);

        return response()->download(storage_path('app/' . $filename));
    }
}

