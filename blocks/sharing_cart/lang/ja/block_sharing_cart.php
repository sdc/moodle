<?php // $Id: block_sharing_cart.php 905 2012-12-05 05:36:52Z malu $

$string['pluginname'] = '共有カート';
$string['sharing_cart:addinstance'] = '新しい共有カートブロックを追加する';

$string['backup'] = '共有カートへコピー';
$string['restore'] = 'コースへコピー';
$string['movedir'] = 'フォルダ移動';
$string['copyhere'] = 'ここにコピー';
$string['notarget'] = 'ターゲットが見つかりません';
$string['clipboard'] = 'この共有アイテムをコピーする';
$string['bulkdelete'] = '一括削除';
$string['confirm_backup'] = '共有カートにコピーしますか？';
$string['confirm_userdata'] = '共有カートへのコピーにユーザーデータを含めますか？';
$string['confirm_restore'] = 'コースにコピーしますか？';
$string['confirm_delete'] = '削除してよろしいですか？';
$string['confirm_delete_selected'] = '選択したアイテムを全て削除してもよろしいですか？';
$string['download'] = 'ダウンロード';

$string['conf:userdata_copyable_modtypes'] = 'ユーザーデータをコピー可能なモジュールタイプ';
$string['conf:userdata_copyable_modtypes_desc'] = '共有カートへコピーする際、コピーしようとしているモジュールがここでチェックを付けたモジュールタイプで、
かつ、操作しているユーザーが <strong>moodle/backup:userinfo</strong> ケイパビリティを持っていれば、
そのモジュールに付随するユーザーデータをコピーに含めるかどうかを選択するダイアログを表示します。<br />
(既定では「マネージャ」ロールのみが <strong>moodle/backup:userinfo</strong> ケイパビリティを持ちます。)';
$string['conf:workaround_qtypes'] = 'リストア不具合対策を行う問題タイプ';
$string['conf:workaround_qtypes_desc'] = 'チェックを付けた問題タイプに対して、リストア不具合対策を行います。
これを有効にすると、リストアしようとしている問題と全く同じ問題が既に存在していて、
しかしながらそのデータに破損が見つかった場合、既存データの再利用を避け、
その問題を再度リストアするように試みます。この対策は、
<i>error_question_match_sub_missing_in_db</i> などのエラー回避に有用です。';

$string['err:invalid'] = '無効な操作です';
$string['err:record_id'] = '不正な共有アイテムIDです';
$string['err:course_id'] = '不正なコースIDです';
$string['err:section_id'] = '不正なセクションです';
$string['err:module_id'] = '不正なモジュールIDです';
$string['err:capability'] = 'この共有アイテムにアクセスする権限がありません';
$string['err:backup'] = 'バックアップ中にエラーが発生しました';
$string['err:restore'] = '復元中にエラーが発生しました';
$string['err:move'] = '共有アイテムの移動に失敗しました';
$string['err:delete'] = '共有アイテムの削除に失敗しました';
$string['err:record'] = 'DBアクセス時にエラーが発生しました';
$string['err:tempdir'] = '一時ディレクトリの作成に失敗しました';
$string['err:cleanup'] = 'クリーンアップ時にエラーが発生しました';
$string['err:unsupported'] = 'バックアップをサポートしていないモジュールです';
$string['err:requireajax'] = 'Sharing Cart requires AJAX';

$string['sharing_cart'] = $string['pluginname'];
$string['sharing_cart_help'] = file_get_contents(__DIR__.'/help/sharing_cart.html');
