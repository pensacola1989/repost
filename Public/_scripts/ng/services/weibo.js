/**
 * Created by weiwei on 3/16/2015.
 */

app.factory('weibo', function() {

    return {
        isWeiboLogin: function() {
            var status = WB2.checkLogin();
            return status;
        }
    };
})
