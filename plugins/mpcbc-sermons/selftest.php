<?php
/** Run: php selftest.php   or   wp mpcbc-sermons selftest. Returns a list of failure messages. */
require_once __DIR__ . '/parser.php';

function mpcbc_sermons_selftest() {
	$cases = array(
		array( 'zh', '希伯來書系列 (十二): "因信而前行(一)"  希伯來書 11:1-22  ~  張健庭牧師博士  慕道園華人浸信會 (MPCBC) 粵語堂主日崇拜 11/01/2026 11:00am PDT',
			array( 'series' => '希伯來書系列', 'title' => '因信而前行(一)', 'scripture' => '希伯來書 11:1-22', 'speaker' => '張健庭牧師博士', 'ministries' => array( 'cantonese' ), 'date' => '2026-11-01', 'ok' => true ) ),
		array( 'zh', '重塑生命系列 (五): "忿怒与温柔" 雅各书 1:19-21 ~ 张健庭牧师博士 2026年 11月 01日 9:00am 国语堂崇拜直播',
			array( 'series' => '重塑生命系列', 'title' => '忿怒与温柔', 'scripture' => '雅各书 1:19-21', 'speaker' => '张健庭牧师博士', 'ministries' => array( 'mandarin' ), 'date' => '2026-11-01', 'ok' => true ) ),
		array( 'zh', '"植堂的基因—生命﹑門訓﹑聖靈"  使徒行傳 9:31 ~ 李約翰牧師  慕道園華人浸信會 (MPCBC) 粵語堂主日崇拜 10/18/2026 11:00am PDT',
			array( 'series' => '', 'title' => '植堂的基因—生命﹑門訓﹑聖靈', 'speaker' => '李約翰牧師', 'ministries' => array( 'cantonese' ), 'date' => '2026-10-18', 'ok' => true ) ),
		array( 'zh', '希伯來書系列 (四): 信仰後退的危機   希伯來書 3:1-4:13 ~ 張健庭牧師博士  慕道園華人浸信會 (MPCBC) 粵語堂主日崇拜 07/26/2026 11:00am PDT',
			array( 'series' => '希伯來書系列', 'title' => '信仰後退的危機', 'scripture' => '希伯來書 3:1-4:13', 'speaker' => '張健庭牧師博士', 'ministries' => array( 'cantonese' ), 'ok' => true ) ),
		array( 'zh', '希伯來書系列 (十) : "永遠完全的救贖"   希伯來書 10:1-8 ~ 張健庭牧師博士  慕道園華人浸信會 (MPCBC) 粵語堂主日崇拜 09/06/2026 11:00am PDT',
			array( 'series' => '希伯來書系列', 'title' => '永遠完全的救贖', 'scripture' => '希伯來書 10:1-8', 'speaker' => '張健庭牧師博士', 'ok' => true ) ),
		array( 'zh', '"神国发展的奥秘——从渴望明白道开始"  马可福音 4:1-20 ~ 路长久弟兄  慕道园华人浸信会 (MPCBC) 国﹑粤语堂联合崇拜 09/13/2026 9:00am PDT',
			array( 'title' => '神国发展的奥秘——从渴望明白道开始', 'speaker' => '路长久弟兄', 'ministries' => array( 'cantonese', 'mandarin' ), 'date' => '2026-09-13', 'ok' => true ) ),
		array( 'en', 'Renovation of the Heart (5): “Anger and Meekness”  James 1:19-21 ~ Rev. Dr. Caleb Cheung',
			array( 'series' => 'Renovation of the Heart', 'title' => 'Anger and Meekness', 'scripture' => 'James 1:19-21', 'speaker' => 'Rev. Dr. Caleb Cheung', 'ministries' => array( 'english' ), 'ok' => true ) ),
		array( 'en', '“Who Has Been Paying Attention?”  Joshua 2  ~  Rev. Edward Lu',
			array( 'series' => '', 'title' => 'Who Has Been Paying Attention?', 'scripture' => 'Joshua 2', 'speaker' => 'Rev. Edward Lu', 'ok' => true ) ),
		array( 'zh', '"新以色列的诞生~从群众到门徒" 马可福音 3:7-19 ~ 路长久弟兄 2026年 08月 09日 9:00am 国语堂崇拜直播',
			array( 'title' => '新以色列的诞生~从群众到门徒', 'scripture' => '马可福音 3:7-19', 'speaker' => '路长久弟兄', 'ministries' => array( 'mandarin' ), 'ok' => true ) ),
		array( 'zh', '約翰福音 (二十): 被揀選群體的興起  ~  張健庭牧師博士  慕道園華人浸信會 (MPCBC)  粵語堂主日崇拜 10/27/2024 11:00am PDT',
			array( 'series' => '約翰福音', 'title' => '被揀選群體的興起', 'scripture' => '', 'speaker' => '張健庭牧師博士', 'ministries' => array( 'cantonese' ), 'date' => '2024-10-27', 'ok' => true ) ),
		array( 'zh', '慕道園華人浸信會(MPCBC)粵語堂主日崇拜 10/17/21 11:15am PDT 題目: 歌羅西書系列(一): 在光明中同得基業   講員: 張健庭牧師',
			array( 'series' => '歌羅西書系列', 'title' => '在光明中同得基業', 'speaker' => '張健庭牧師', 'ministries' => array( 'cantonese' ), 'date' => '2021-10-17', 'ok' => true ) ),
		array( 'en', '03.19.2023 CONNECT MINISTRY- “A Case Against Humanity” Micah 6:1-8 ~ Pastor Edward Lu',
			array( 'title' => 'A Case Against Humanity', 'scripture' => 'Micah 6:1-8', 'speaker' => 'Pastor Edward Lu', 'ministries' => array( 'english' ), 'date' => '2023-03-19', 'ok' => true ) ),
		array( 'zh', '"Staying on course in the track of faith" ~ Rev. Caleb Cheung, PhD  "在信心的跑道上堅持"  ~  張健庭牧師博士',
			array( 'title' => 'Staying on course in the track of faith / 在信心的跑道上堅持', 'speaker' => 'Rev. Caleb Cheung, PhD', 'ministries' => array( 'cantonese', 'mandarin', 'english' ), 'ok' => true ) ),
		array( 'zh', '每日灵修：6月08日 # 约翰福音 # 暗夜大光 # 莱尔著 (John Charles Ryle，1816-1900)', array( 'ok' => false ) ),
		array( 'zh', '08/30/2026 MPCBC CONNECT MINISTRY', array( 'ok' => false ) ),
		array( 'zh', 'Cross-Congregational Testimony ~ Sister Carmen Chan', array( 'ok' => false ) ),
	);
	$fails = array();
	foreach ( $cases as $i => $c ) {
		$got = mpcbc_sermons_parse( $c[1], $c[0] );
		foreach ( $c[2] as $k => $want ) {
			if ( $got[ $k ] !== $want ) $fails[] = "case $i field $k: want " . json_encode( $want, JSON_UNESCAPED_UNICODE ) . ' got ' . json_encode( $got[ $k ], JSON_UNESCAPED_UNICODE );
		}
	}
	return $fails;
}

if ( PHP_SAPI === 'cli' && ! defined( 'ABSPATH' ) && realpath( $argv[0] ) === __FILE__ ) {
	$f = mpcbc_sermons_selftest();
	echo $f ? implode( "\n", $f ) . "\nFAILED\n" : "OK: parser selftest passed\n";
	exit( $f ? 1 : 0 );
}
