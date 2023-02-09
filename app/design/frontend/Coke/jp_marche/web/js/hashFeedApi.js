define([
    'jquery'
], function ($) {
    'use strict';

// <div data-mage-init='{"js/hashFeedApi": {"version":"01"}}'>
// <div class="campApi"></div>
// </div>

    $.widget('mage.hashFeedApi', {
        options: {
            version: null,
            url1: "https://campwidget.com/api_coke_01/camp-twig-api.php",
            url2: "https://campwidget.com/api_coke_02/camp-twig-api.php",
            url3: "https://campwidget.com/api_coke_03/camp-twig-api.php",
            elm: ".campApi",
            params: "",
            no_image_url: false,
            count: 16,
            maxResults: 32,
            testData: {
                "code": "200",
                "status": "success",
                "post_items": [
                    {
                        "id": 12431,
                        "account": "",
                        "user_name": "",
                        "profile_image_url": "",
                        "contents": "本日は檸檬堂定番レモンで( ﾟ∀ﾟ)ﾉБ□ ｶﾝﾊﾟｰｲ\nおつまみ？はカツカレー！\n私的にはカレーもおつまみ、トンカツもおつまみ…おつまみ×おつまみ最強じゃね？(・∀・)ウマシ!\n油もレモンサワーでさっぱり！組み合わせ良きかな\n#晩酌 #コカコーラ #CocaCola #檸檬堂 #lemondou #lemondo #定番レモン #レモンサワー #lemonsour #lemon #カツカレー #katsucurry",
                        "hash_product": ",晩酌,コカコーラ,CocaCola,檸檬堂,lemondou,lemondo,定番レモン,レモンサワー,lemonsour,lemon,カツカレー,katsucurry,",
                        "posting_time": "2022/01/19 16:11:21",
                        "images": "https://campwidget.com/img-instagram/tool-hashtag/45/20220119/17900312522370812.png",
                        "sns_id": "17900312522370812",
                        "id_str": "17900312522370812",
                        "front_img_flg": 0,
                        "is_approved": 1,
                        "sns_type": 1,
                        "media_type": "IMAGE",
                        "comments_count": 0,
                        "like_count": 2,
                        "permalink": "https://www.instagram.com/p/CY5x3N9lNaW/",
                        "created": {
                            "date": "2022-01-19 16:20:09.000000",
                            "timezone_type": 3,
                            "timezone": "Asia/Tokyo"
                        },
                        "modified": {
                            "date": "2022-01-19 16:20:09.000000",
                            "timezone_type": 3,
                            "timezone": "Asia/Tokyo"
                        },
                        "location": null,
                        "description": null,
                        "protected": null,
                        "followers_count": null,
                        "friends_count": null,
                        "statuses_count": null,
                        "favourites_count": null,
                        "time_zone": null,
                        "geo_enabled": null,
                        "profile_background_image_url": null,
                        "coordinates": null,
                        "url": null,
                        "favorite_count": null,
                        "retweet_count": null,
                        "posted_date": {
                            "date": "2022-01-19 16:11:21.000000",
                            "timezone_type": 3,
                            "timezone": "Asia/Tokyo"
                        },
                        "is_drew_lots1": 0,
                        "is_drew_lots2": 0,
                        "is_drew_lots3": 0,
                        "is_drew_lots4": 0,
                        "is_drew_lots5": 0,
                        "is_drew_lots6": 0,
                        "are_sent_dms": null,
                        "are_sent_dm_status": null,
                        "note": null,
                        "following": null
                    },
                    {
                        "id": 12402,
                        "account": "",
                        "user_name": "",
                        "profile_image_url": "",
                        "contents": "🥤🥤🥤🥤🥤🥤🥤🥤🥤🥤🥤🥤🥤\n\nおやつの時間もカプサイメン✨\nコーラと辛麺はセットでしょ？！\n\n🥤🥤🥤🥤🥤🥤🥤🥤🥤🥤🥤🥤🥤🥤🥤\n\n#カプサイメン#辛いもの好き#辛いもの#辛いもの好きな人と繋がりたい #辛いの大好き #辛いラーメン #辛い食べ物 #岐阜タンメン#姉妹店#岐阜#一宮#ラーメン女子\n#コカコーラ#コカコーラ部 #コカコーラ好きな人と繋がりたい #ラーメン#Ramen#ramenya#goto#gotoキャンペーン #一宮ランチ #岐阜ラーメン#深夜営業",
                        "hash_product": ",カプサイメン,辛いもの好き,辛いもの,辛いもの好きな人と繋がりたい,辛いの大好き,辛いラーメン,辛い食べ物,岐阜タンメン,姉妹店,岐阜,一宮,ラーメン女子,コカコーラ,コカコーラ部,コカコーラ好きな人と繋がりたい,ラーメン,Ramen,ramenya,goto,gotoキャンペーン,一宮ランチ,岐阜ラーメン,深夜営業,",
                        "posting_time": "2022/01/19 14:26:35",
                        "images": "https://campwidget.com/img-instagram/tool-hashtag/45/20220119/17904194153320112.png",
                        "sns_id": "17904194153320112",
                        "id_str": "17904194153320112",
                        "front_img_flg": 0,
                        "is_approved": 1,
                        "sns_type": 1,
                        "media_type": "CAROUSEL_ALBUM",
                        "comments_count": 0,
                        "like_count": 6,
                        "permalink": "https://www.instagram.com/p/CY5l39DlIaJ/",
                        "created": {
                            "date": "2022-01-19 14:30:09.000000",
                            "timezone_type": 3,
                            "timezone": "Asia/Tokyo"
                        },
                        "modified": {
                            "date": "2022-01-19 14:30:09.000000",
                            "timezone_type": 3,
                            "timezone": "Asia/Tokyo"
                        },
                        "location": null,
                        "description": null,
                        "protected": null,
                        "followers_count": null,
                        "friends_count": null,
                        "statuses_count": null,
                        "favourites_count": null,
                        "time_zone": null,
                        "geo_enabled": null,
                        "profile_background_image_url": null,
                        "coordinates": null,
                        "url": null,
                        "favorite_count": null,
                        "retweet_count": null,
                        "posted_date": {
                            "date": "2022-01-19 14:26:35.000000",
                            "timezone_type": 3,
                            "timezone": "Asia/Tokyo"
                        },
                        "is_drew_lots1": 0,
                        "is_drew_lots2": 0,
                        "is_drew_lots3": 0,
                        "is_drew_lots4": 0,
                        "is_drew_lots5": 0,
                        "is_drew_lots6": 0,
                        "are_sent_dms": null,
                        "are_sent_dm_status": null,
                        "note": null,
                        "following": null
                    },
                    {
                        "id": 11944,
                        "account": "Lmyu1028",
                        "user_name": "L.myu.(coca-colaに恋した写真家)",
                        "profile_image_url": "http://pbs.twimg.com/profile_images/1480914592767541256/aP2Bmp4M_normal.jpg",
                        "contents": "首を傾げるコカ・コーラ\n\n#Lmyuはいいぞ\n#ファインダー越しの私の世界\n#キリトリセカイ\n#写真好きな人と繋がりたい\n#コカコーラ\n#cocacola https://t.co/cIlCjedMXu",
                        "hash_product": ",Lmyuはいいぞ,ファインダー越しの私の世界,キリトリセカイ,写真好きな人と繋がりたい,コカコーラ,cocacola,",
                        "posting_time": "2022/01/17 12:26:15",
                        "images": "https://pbs.twimg.com/media/FJRhSsmagAEaFw2.jpg",
                        "sns_id": "1424285044902809601",
                        "id_str": "1482917154010058757",
                        "front_img_flg": 0,
                        "is_approved": 1,
                        "sns_type": 0,
                        "media_type": "IMAGE",
                        "comments_count": 0,
                        "like_count": 0,
                        "permalink": "https://twitter.com/Lmyu1028/status/1482917154010058757",
                        "created": {
                            "date": "2022-01-17 12:30:02.000000",
                            "timezone_type": 3,
                            "timezone": "Asia/Tokyo"
                        },
                        "modified": {
                            "date": "2022-01-17 12:30:02.000000",
                            "timezone_type": 3,
                            "timezone": "Asia/Tokyo"
                        },
                        "location": "Fukuoka",
                        "description": "大学生兼カメラマン📸\n\nカメラ📸と作詞🖊をしています😀\n\n気軽にエルと呼んでね\n\nコカ・コーラが大好き🥤\n\n取材・お仕事の依頼はDMへ\n写真の利用許可もDMで気軽に尋ねてね😊\n\nNikonD5600📸\n\n※写真の無断使用は見つけ次第、厳重に対処します",
                        "protected": "",
                        "followers_count": 1584,
                        "friends_count": 2214,
                        "statuses_count": 3620,
                        "favourites_count": 8290,
                        "time_zone": null,
                        "geo_enabled": "",
                        "profile_background_image_url": null,
                        "coordinates": null,
                        "url": "https://t.co/TH8UHRwCYl",
                        "favorite_count": 3,
                        "retweet_count": 0,
                        "posted_date": {
                            "date": "2022-01-17 12:26:15.000000",
                            "timezone_type": 3,
                            "timezone": "Asia/Tokyo"
                        },
                        "is_drew_lots1": 0,
                        "is_drew_lots2": 0,
                        "is_drew_lots3": 0,
                        "is_drew_lots4": 0,
                        "is_drew_lots5": 0,
                        "is_drew_lots6": 0,
                        "are_sent_dms": null,
                        "are_sent_dm_status": null,
                        "note": null,
                        "following": 0
                    }
                ]
            },
            testMode: false
        },

        /**
         * @private
         */
        _create: function () {
            let self = this;
            console.log(this.options.version);
            this._fetchData();

            $(this.options.elm).on('click', '.loadMore', function() {
                console.log('clicked');
                self.loadMore();
            });
        },

        _fetchData: function() {
            let self = this;
            $(self.options.elm).html("");
            let apiUrl = '';
            switch (self.options.version) {
                case '01':
                    apiUrl = self.options.url1;
                    break;
                case '02':
                    apiUrl = self.options.url2;
                    break;
                case '03':
                    apiUrl = self.options.url3;
                    break;
                default:
                    apiUrl = self.options.url1;
            }
            console.log(apiUrl);
            //var set_data = getUrlVars(this.send_data);
            $.ajax({
                type: 'get',
                url: apiUrl,
                dataType : "json",
                //data: set_data,
                context:this,
            }).done(function(resdata) {
                if (self.options.testMode) {
                    resdata = self.options.testData
                }
                console.log(resdata);
                if (resdata && resdata.code==200) {
                    self.renderList(resdata, self.options);
                }

            }).fail(function(resdata) {
                var errors;
                //var split_index;
                var error_disp = "";

                if(this._isJSON(resdata.responseText)===true){
                    errors = resdata.responseText;
                    console.log("----------------------error");
                    console.log(errors);
                    return false;
                    if(errors){
                        $.each(errors,function(index,val){
                            console.log("----------------------error");
                            console.log(val);
                            console.log("----------------------");
                            if(val.photo){
                                error_disp = error_disp + val.photo + "<br>";
                            }
                        });
                    }
                    if(error_disp){
                        console.log("----------------------error:" + error_disp);
                        return false;
                    }else{
                        console.log("----------------------server error");
                        return false;
                    }
                }else{
                    console.log("----------------------connection error");
                    return false;
                }
            });
        },

        _isJSON: function(arg) {
            arg = (typeof arg === "function") ? arg() : arg;
            if (typeof arg  !== "string") {
                return false;
            }
            try {
                arg = (!JSON) ? eval("(" + arg + ")") : JSON.parse(arg);
                return true;
            } catch (e) {
                return false;
            }
        },

        renderList: function(resdata, obj) {
            let self = this;
            for (var i = 0; i < resdata.post_items.length; i++) {
                if(i >= self.options.maxResults){
                    break;
                }
                let htmlDiv = '';
                htmlDiv = $(document.createElement('div')).attr('class','campHashDiv');
                var media_url = (resdata.post_items[i].images) ? resdata.post_items[i].images : this.options.no_image_url;
                if(media_url){
                    htmlDiv.append('<div class="image">'+'<img src="'+ media_url +'" width="310px">'+'</div>');
                }
                htmlDiv.append('<div class="contents">'+ resdata.post_items[i].contents +'</div>');
                // htmlDiv.append('<div class="account">'+ resdata.post_items[i].account +'</div>');

                //Load more setup
                if(i === self.options.count){
                    $(obj.elm).append('<div class="flex-full"><div class="loadMore action primary"><span>もっとみる</span></div></div>');
                }

                if(i >= self.options.count){
                    htmlDiv.addClass('hide').attr('style','display:none');
                }

                $(obj.elm).append(htmlDiv);
                /*
                取得できるデータ
                profile_image_url
                account
                contents
                hash_product
                id_str
                images
                profile_image_url
                media_type
                permalink
                etc...
                */
            }
        },

        loadMore: function () {
            $('.loadMore').hide();
            $('.flex-full').hide();
            $('.campHashDiv').removeClass('hide').show();
        }
    });

    return $.mage.hashFeedApi;
});
