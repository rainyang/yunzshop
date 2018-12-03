//update 2018-12-03 11:47:41
let popularize = {"wechat":[],"mini":[],"wap":[],"app":[],"alipay":[]};
if (typeof define === "function") {
    define(popularize)
} else {
    window.$popularize = popularize;
}