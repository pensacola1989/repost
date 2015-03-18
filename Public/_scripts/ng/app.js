/**
 * Created by weiwei on 3/16/2015.
 */

var app = angular.module('app', ['ngRoute'], function($interpolateProvider) {
    $interpolateProvider.startSymbol('{%');
    $interpolateProvider.endSymbol('%}');
});

app.config(['$routeProvider', function($routeProvider) {
    $routeProvider
        .when('/index', { templateUrl: _viewPathBase + 'index.html',  controller: 'repostCtrl'})
        .otherwise({redirectTo: '/index'});
}]);

