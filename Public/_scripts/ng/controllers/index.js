/**
 * Created by weiwei on 3/16/2015.
 */

app.controller('repostCtrl', function ($scope, weibo) {

    if(!weibo.isWeiboLogin) {
        weibo.login();
    } else {

    }
});

