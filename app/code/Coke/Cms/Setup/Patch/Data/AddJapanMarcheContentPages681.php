<?php

namespace Coke\Cms\Setup\Patch\Data;

use Coke\Cms\Helper\Data;
use Coke\Cms\Model\ContentUpgrader;
use Magento\Framework\Setup\ModuleDataSetupInterface;

class AddJapanMarcheContentPages681 implements \Magento\Framework\Setup\Patch\DataPatchInterface
{
    /**
     * @var ModuleDataSetupInterface
     */
    private $moduleDataSetup;
    /**
     * @var ContentUpgrader
     */
    private $contentUpgrader;
    /**
     * @var Data
     */
    private $helper;

    /**
     * UpdateHomePage constructor.
     * @param ModuleDataSetupInterface $moduleDataSetup
     * @param ContentUpgrader $contentUpgrader
     * @param Data $helper
     */
    public function __construct(
        ModuleDataSetupInterface $moduleDataSetup,
        ContentUpgrader $contentUpgrader,
        Data $helper
    ) {
        $this->moduleDataSetup = $moduleDataSetup;
        $this->contentUpgrader = $contentUpgrader;
        $this->helper = $helper;
    }

    /**
     * @inheritdoc
     */
    public static function getDependencies()
    {
        return [];
    }

    /**
     * @inheritdoc
     */
    public function getAliases()
    {
        return [];
    }

    /**
     * @return $this|\Magento\Framework\Setup\Patch\DataPatchInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function apply()
    {
        $japanMarche = $this->helper->getJapanMarcheJapaneseStore();
        $this->moduleDataSetup->getConnection()->startSetup();

        $this->contentUpgrader->upgradePagesByStoreView([
            'labelless-test' => [
                'title' => 'ラベルレスボトルが増えているのはなぜ？人気の理由を紹介',
                'content_heading' => '',
                'identifier' => 'labelless-test',
                'stores' => [$japanMarche->getId()],
                'store_id' => [$japanMarche->getId()],
                'page_layout' => 'cms-full-width',
                'meta_title' => 'ラベルレスボトルが増えているのはなぜ？人気の理由を紹介',
                'meta_keywords' => 'ラベルレス,ペットボトル,ラベルレスボトル,なぜ,理由',
                'meta_description' => '近年、ラベルを付けずに販売する「ラベルレスボトル」が急速に広がりを見せています。この記事では、なぜラベルレスボトルが注目を集めているのか、人気の理由を紹介します。',
                'is_active' => 1
            ],
            'gift_1' => [
                'title' => '内祝いに最適な名入れギフトのおすすめ商品や選び方を紹介',
                'content_heading' => '',
                'identifier' => 'gift_1',
                'stores' => [$japanMarche->getId()],
                'store_id' => [$japanMarche->getId()],
                'page_layout' => 'cms-full-width',
                'meta_title' => '内祝いに最適な名入れギフトのおすすめ商品や選び方を紹介',
                'meta_keywords' => '内祝い,名入れ,ギフト',
                'meta_description' => 'この記事では、内祝いに名入れギフトが人気の理由や選び方、おすすめのアイテムを紹介します。',
                'is_active' => 1
            ],
            'gift_2' => [
                'title' => '男性におすすめの名入れギフトはどれ？人気のアイテムを紹介',
                'content_heading' => '',
                'identifier' => 'gift_2',
                'stores' => [$japanMarche->getId()],
                'store_id' => [$japanMarche->getId()],
                'page_layout' => 'cms-full-width',
                'meta_title' => '男性におすすめの名入れギフトはどれ？人気のアイテムを紹介',
                'meta_keywords' => '名入れギフト,男性',
                'meta_description' => 'この記事では、男性にプレゼントする名入れギフトの選び方や、おすすめの名入れギフトを紹介します。',
                'is_active' => 1
            ],
            'gift_3' => [
                'title' => '女性におすすめの名入れギフトはどれ？人気の商品を紹介',
                'content_heading' => '',
                'identifier' => 'gift_3',
                'stores' => [$japanMarche->getId()],
                'store_id' => [$japanMarche->getId()],
                'page_layout' => 'cms-full-width',
                'meta_title' => '女性におすすめの名入れギフトはどれ？人気の商品を紹介',
                'meta_keywords' => '名入れギフト,女性',
                'meta_description' => 'この記事では、女性に贈る名入れギフトの選び方やおすすめのアイテムを紹介します。',
                'is_active' => 1
            ],
            'gift_4' => [
                'title' => 'お菓子やお酒に名入れはできる？おすすめシーンや人気商品を紹介',
                'content_heading' => '',
                'identifier' => 'gift_4',
                'stores' => [$japanMarche->getId()],
                'store_id' => [$japanMarche->getId()],
                'page_layout' => 'cms-full-width',
                'meta_title' => 'お菓子やお酒に名入れはできる？おすすめシーンや人気商品を紹介',
                'meta_keywords' => '名入れ,お菓子,お酒',
                'meta_description' => 'この記事では、お菓子やお酒の名入れギフトについて、おすすめのシーンや人気の商品を紹介します。',
                'is_active' => 1
            ],
            'labelless_0' => [
                'title' => 'ラベルレスのペットボトル飲料が人気！メリットや意味を徹底解説',
                'content_heading' => '',
                'identifier' => 'labelless_0',
                'stores' => [$japanMarche->getId()],
                'store_id' => [$japanMarche->getId()],
                'page_layout' => 'cms-full-width',
                'meta_title' => 'ラベルレスのペットボトル飲料が人気！メリットや意味を徹底解説',
                'meta_keywords' => 'ラベルレス,メリット,意味',
                'meta_description' => 'この記事では、近年人気が高まっているラベルレスペットボトルの意味やメリットについて解説します。',
                'is_active' => 1
            ],
            'labelless_2' => [
                'title' => 'ラベルレスボトルは安い？値段の傾向やおすすめポイントとは',
                'content_heading' => '',
                'identifier' => 'labelless_2',
                'stores' => [$japanMarche->getId()],
                'store_id' => [$japanMarche->getId()],
                'page_layout' => 'cms-full-width',
                'meta_title' => 'ラベルレスボトルは安い？値段の傾向やおすすめポイントとは',
                'meta_keywords' => 'ラベルレスボトル,安い',
                'meta_description' => 'この記事では、近年人気が高まっているラベルレスボトルについて、通常のペットボトルよりも値段は安いのかということや、おすすめポイントを解説します。',
                'is_active' => 1
            ],
            'labelless_3' => [
                'title' => '水・ミネラルウォーターはラベルレスを選ぼう！メリットやおすすめポイントとは',
                'content_heading' => '',
                'identifier' => 'labelless_3',
                'stores' => [$japanMarche->getId()],
                'store_id' => [$japanMarche->getId()],
                'page_layout' => 'cms-full-width',
                'meta_title' => '水・ミネラルウォーターはラベルレスを選ぼう！メリットやおすすめポイントとは',
                'meta_keywords' => 'ラベルレス,水,ミネラルウォーター',
                'meta_description' => 'この記事では、ラベルレスの水やミネラルウォーターを購入した方の口コミを参考に、ラベルレスの魅力を解説します。',
                'is_active' => 1
            ],
            'recycle_1' => [
                'title' => 'ペットボトルをリサイクルしよう！必要性や方法、問題点について',
                'content_heading' => '',
                'identifier' => 'recycle_1',
                'stores' => [$japanMarche->getId()],
                'store_id' => [$japanMarche->getId()],
                'page_layout' => 'cms-full-width',
                'meta_title' => 'ペットボトルをリサイクルしよう！必要性や方法、問題点について',
                'meta_keywords' => 'リサイクル,ペットボトル,何になる',
                'meta_description' => 'この記事では、ペットボトルのリサイクルに焦点を当て、必要性やリサイクルすると何になるのか、問題点などを解説します。',
                'is_active' => 1
            ],
            'recycle_2' => [
                'title' => '家庭や企業でのリサイクルの具体例を紹介！今日からできることは？',
                'content_heading' => '',
                'identifier' => 'recycle_2',
                'stores' => [$japanMarche->getId()],
                'store_id' => [$japanMarche->getId()],
                'page_layout' => 'cms-full-width',
                'meta_title' => '家庭や企業でのリサイクルの具体例を紹介！今日からできることは？',
                'meta_keywords' => 'リサイクル,例,家庭,具体例,例えば',
                'meta_description' => 'この記事では、家庭や企業で今日からできるリサイクルの具体的な例を紹介します。',
                'is_active' => 1
            ],
            'recycle_3' => [
                'title' => 'リサイクルの目的、メリット・デメリットを解説！ごみと環境、資源に与える影響とは',
                'content_heading' => '',
                'identifier' => 'recycle_3',
                'stores' => [$japanMarche->getId()],
                'store_id' => [$japanMarche->getId()],
                'page_layout' => 'cms-full-width',
                'meta_title' => 'リサイクルの目的、メリット・デメリットを解説！ごみと環境、資源に与える影響とは',
                'meta_keywords' => 'リサイクル,メリット,デメリット,利点,目的',
                'meta_description' => 'この記事では、リサイクル活動における目的や、メリット・デメリットを紹介します。',
                'is_active' => 1
            ],
            'recycle_4' => [
                'title' => 'リサイクルのためにできることは？ごみを減らすアイデアや取り組みについて',
                'content_heading' => '',
                'identifier' => 'recycle_4',
                'stores' => [$japanMarche->getId()],
                'store_id' => [$japanMarche->getId()],
                'page_layout' => 'cms-full-width',
                'meta_title' => 'リサイクルのためにできることは？ごみを減らすアイデアや取り組みについて',
                'meta_keywords' => 'リサイクル,できること,個人,私たちにできること',
                'meta_description' => 'この記事では、私たち消費者がリサイクルのためにできることを紹介します。',
                'is_active' => 1
            ],
            'interview_1' => [
                'title' => '日々の会話を大切に。Hinoさんの人生に寄り添う、「ありがとう」の連鎖。',
                'content_heading' => '',
                'identifier' => 'interview_1',
                'stores' => [$japanMarche->getId()],
                'store_id' => [$japanMarche->getId()],
                'page_layout' => 'cms-full-width',
                'meta_title' => '日々の会話を大切に。Hinoさんの人生に寄り添う、「ありがとう」の連鎖。',
                'meta_keywords' => 'Myラベル,メッセージ,ギフト,記念,感謝,想い',
                'meta_description' => '2人の娘を育てるHinoさんにインタビューを行い、身近な人との思い出を振り返りながら感謝の想いに包まれたたくさんのお話しを伺いました。',
                'is_active' => 1
            ],
            'interview_2' => [
                'title' => '自分にも、環境にも優しく。ミカエルさんが心に留めるのは、自然と共創するライフスタイル。',
                'content_heading' => '',
                'identifier' => 'interview_2',
                'stores' => [$japanMarche->getId()],
                'store_id' => [$japanMarche->getId()],
                'page_layout' => 'cms-full-width',
                'meta_title' => '自分にも、環境にも優しく。ミカエルさんが心に留めるのは、自然と共創するライフスタイル。',
                'meta_keywords' => 'サスティナブル,エコ,環境,リサイクル,ラベルレス',
                'meta_description' => 'ファッション業界でモデルを務めるミカエルさんにインタビューを行い、環境への考えをひも解きながら、美しい地球を残すためのサスティナブルな選択について語っていただきました。',
                'is_active' => 1
            ],
            'milktea_1' => [
                'title' => 'ミルクティーのおいしい作り方を解説！茶葉・ミルク選びにもこだわろう',
                'content_heading' => '',
                'identifier' => 'milktea_1',
                'stores' => [$japanMarche->getId()],
                'store_id' => [$japanMarche->getId()],
                'page_layout' => 'cms-full-width',
                'meta_title' => 'ミルクティーのおいしい作り方を解説！茶葉・ミルク選びにもこだわろう',
                'meta_keywords' => 'ミルクティー,作り方,入れ方,ティーバッグ',
                'meta_description' => 'この記事では、ミルクティーに合う紅茶の茶葉の種類や、おいしいミルクティーのいれ方を紹介します。',
                'is_active' => 1
            ],
            'lemontea_1' => [
                'title' => 'レモンティーの作り方やおいしい飲み方、アレンジレシピを紹介',
                'content_heading' => '',
                'identifier' => 'lemontea_1',
                'stores' => [$japanMarche->getId()],
                'store_id' => [$japanMarche->getId()],
                'page_layout' => 'cms-full-width',
                'meta_title' => 'レモンティーの作り方やおいしい飲み方、アレンジレシピを紹介',
                'meta_keywords' => 'レモンティー,作り方,飲み方',
                'meta_description' => 'この記事では、自宅でのレモンティーの作り方やアレンジレシピを紹介します。',
                'is_active' => 1
            ],
            'mineralwater_0' => [
                'title' => 'ミネラルウォーターとは？定義や種類、製造工程を紹介',
                'content_heading' => '',
                'identifier' => 'mineralwater_0',
                'stores' => [$japanMarche->getId()],
                'store_id' => [$japanMarche->getId()],
                'page_layout' => 'cms-full-width',
                'meta_title' => 'ミネラルウォーターとは？定義や種類、製造工程を紹介',
                'meta_keywords' => 'ミネラルウォーター,定義,ミネラルウォーターとは,種類',
                'meta_description' => 'この記事では、ミネラルウォーターの定義やどんな種類があるのか、製造工程などを紹介します。',
                'is_active' => 1
            ],
            'mineralwater_1' => [
                'title' => 'ミネラルウォーターのおすすめは？選び方や飲み方を解説',
                'content_heading' => '',
                'identifier' => 'mineralwater_1',
                'stores' => [$japanMarche->getId()],
                'store_id' => [$japanMarche->getId()],
                'page_layout' => 'cms-full-width',
                'meta_title' => 'ミネラルウォーターのおすすめは？選び方や飲み方を解説',
                'meta_keywords' => 'ミネラルウォーター,おすすめ,選び方',
                'meta_description' => 'この記事では、ミネラルウォーターの選び方や飲んだほうが良い理由、いつ飲むと良いのかなどを紹介します。',
                'is_active' => 1
            ],
            'mineralwater_2' => [
                'title' => 'ミネラルウォーターの硬水と軟水って何？硬度の違いや使い分け方を解説',
                'content_heading' => '',
                'identifier' => 'mineralwater_2',
                'stores' => [$japanMarche->getId()],
                'store_id' => [$japanMarche->getId()],
                'page_layout' => 'cms-full-width',
                'meta_title' => 'ミネラルウォーターの硬水と軟水って何？硬度の違いや使い分け方を解説',
                'meta_keywords' => 'ミネラルウォーター,硬水,軟水',
                'meta_description' => 'この記事では、ミネラルウォーターの違いや、使い分ける方法を紹介します。',
                'is_active' => 1
            ],
            'mineralwater_3' => [
                'title' => '天然水とは？ミネラルウォーターや水道水との違いや活用方法を解説',
                'content_heading' => '',
                'identifier' => 'mineralwater_3',
                'stores' => [$japanMarche->getId()],
                'store_id' => [$japanMarche->getId()],
                'page_layout' => 'cms-full-width',
                'meta_title' => '天然水とは？ミネラルウォーターや水道水との違いや活用方法を解説',
                'meta_keywords' => '天然水,天然水とは',
                'meta_description' => 'この記事では、天然水の定義やミネラルウォーター・水道水との違い、おすすめの活用方法について紹介します。',

                'is_active' => 1
            ],
            'orangejuice_1' => [
                'title' => '100パーセントのオレンジジュースにはどんな種類がある？選ぶポイントを解説',
                'content_heading' => '',
                'identifier' => 'orangejuice_1',
                'stores' => [$japanMarche->getId()],
                'store_id' => [$japanMarche->getId()],
                'page_layout' => 'cms-full-width',
                'meta_title' => '100パーセントのオレンジジュースにはどんな種類がある？選ぶポイントを解説',
                'meta_keywords' => 'オレンジジュース,100パーセント,種類',
                'meta_description' => 'この記事では、100パーセントオレンジジュースの種類や選び方を紹介します。',
                'is_active' => 1
            ],
            'caffellatte_1' => [
                'title' => 'カフェラテとカフェオレの違いって何？カプチーノやカフェモカとの違いも解説',
                'content_heading' => '',
                'identifier' => 'caffellatte_1',
                'stores' => [$japanMarche->getId()],
                'store_id' => [$japanMarche->getId()],
                'page_layout' => 'cms-full-width',
                'meta_title' => 'カフェラテとカフェオレの違いって何？カプチーノやカフェモカとの違いも解説',
                'meta_keywords' => 'カフェラテとは,カフェラテ,意味,カフェオレ,違い,カプチーノ,カフェモカ ',
                'meta_description' => 'この記事では、カフェラテの定義やカフェオレ・カプチーノ・カフェモカとの違いを解説します。',
                'is_active' => 1
            ],
            'sustainable_1' => [
                'title' => 'サスティナブルとは？SDGsとの違いや企業の取り組みを紹介',
                'content_heading' => '',
                'identifier' => 'sustainable_1',
                'stores' => [$japanMarche->getId()],
                'store_id' => [$japanMarche->getId()],
                'page_layout' => 'cms-full-width',
                'meta_title' => 'サスティナブルとは？SDGsとの違いや企業の取り組みを紹介',
                'meta_keywords' => 'サステナビリティ,意味,サステナブル,サステナブルとは',
                'meta_description' => 'この記事では、サスティナブルとは何か、意味やSDGs・ESGとの違い、コカ･コーラ社でのサスティナブル社会の実現に向けた取り組みについて紹介します。',
                'is_active' => 1
            ],
            'sustainable_2' => [
                'title' => 'サスティナブルな取り組みとは？企業・個人の具体的な事例を紹介',
                'content_heading' => '',
                'identifier' => 'sustainable_2',
                'stores' => [$japanMarche->getId()],
                'store_id' => [$japanMarche->getId()],
                'page_layout' => 'cms-full-width',
                'meta_title' => 'サスティナブルな取り組みとは？企業・個人の具体的な事例を紹介',
                'meta_keywords' => 'サステナビリティ,取り組み,サステナブル,問題,具体的に',
                'meta_description' => 'この記事では、スティナビリティの実現に向けてコカ･コーラ社をはじめとした企業が行う取り組みや、日常生活でも取り入れられるサスティナブルな行動について紹介します。',
                'is_active' => 1
            ],
            'sustainable_3' => [
                'title' => 'サスティナブル商品とは何？おすすめの理由や注目の商品を紹介',
                'content_heading' => '',
                'identifier' => 'sustainable_3',
                'stores' => [$japanMarche->getId()],
                'store_id' => [$japanMarche->getId()],
                'page_layout' => 'cms-full-width',
                'meta_title' => 'サスティナブル商品とは何？おすすめの理由や注目の商品を紹介',
                'meta_keywords' => 'サステナビリティ,商品,サステナブル,製品',
                'meta_description' => 'この記事では、サスティナブル商品とはどんなものか、おすすめの理由や具体的な商品を紹介します。',
                'is_active' => 1
            ],
            'cocoa_1' => [
                'title' => 'ココアとチョコレートは何が違う？製造工程やドリンクの違いを解説',
                'content_heading' => '',
                'identifier' => 'cocoa_1',
                'stores' => [$japanMarche->getId()],
                'store_id' => [$japanMarche->getId()],
                'page_layout' => 'cms-full-width',
                'meta_title' => 'ココアとチョコレートは何が違う？製造工程やドリンクの違いを解説',
                'meta_keywords' => 'ココア,チョコ,違い,チョコレート',
                'meta_description' => 'この記事では、ココアとチョコレートについて、製造工程や味、成分などの違いを紹介します。',
                'is_active' => 1
            ],
            'orangejuice_2' => [
                'title' => 'オレンジジュースの作り方は？簡単にできるアレンジレシピも紹介',
                'content_heading' => '',
                'identifier' => 'orangejuice_2',
                'stores' => [$japanMarche->getId()],
                'store_id' => [$japanMarche->getId()],
                'page_layout' => 'cms-full-width',
                'meta_title' => 'オレンジジュースの作り方は？簡単にできるアレンジレシピも紹介',
                'meta_keywords' => 'オレンジジュース,作り方,ミキサー,手作り',
                'meta_description' => 'この記事では、オレンジジュースの基本的な作り方やミキサーなしでオレンジジュースを作る方法、オレンジジュースのアレンジレシピを紹介します。',
                'is_active' => 1
            ],
            'tea_0' => [
                'title' => '紅茶にはどんな種類がある？茶葉の名前や飲み方、選ぶポイントを解説',
                'content_heading' => '',
                'identifier' => 'tea_0',
                'stores' => [$japanMarche->getId()],
                'store_id' => [$japanMarche->getId()],
                'page_layout' => 'cms-full-width',
                'meta_title' => '紅茶にはどんな種類がある？茶葉の名前や飲み方、選ぶポイントを解説',
                'meta_keywords' => '紅茶,種類,名前,茶葉',
                'meta_description' => 'この記事では、紅茶の茶葉の種類や味・香りの特徴、適した飲み方などを紹介します。',
                'is_active' => 1
            ],
            'tea_1' => [
                'title' => '紅茶の入れ方をタイプ別に紹介！簡単においしく飲む方法はある？',
                'content_heading' => '',
                'identifier' => 'tea_1',
                'stores' => [$japanMarche->getId()],
                'store_id' => [$japanMarche->getId()],
                'page_layout' => 'cms-full-width',
                'meta_title' => '紅茶の入れ方をタイプ別に紹介！簡単においしく飲む方法はある？',
                'meta_keywords' => '紅茶,入れ方,水出し',
                'meta_description' => 'この記事では、紅茶のタイプ別に紅茶の入れ方や手順、おいしく入れるコツを紹介します。',
                'is_active' => 1
            ]
        ]);

        $this->moduleDataSetup->getConnection()->endSetup();

        return $this;
    }
}
