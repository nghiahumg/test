import Vue from "vue";
import Router from "vue-router";

Vue.use(Router);

export default new Router({
    //   mode: "history",
    mode: 'hash',
    base: process.env.BASE_URL,
    scrollBehavior(to, from, savedPosition) {
        if (savedPosition) {
            return savedPosition;
        } else {
            return {x: 0, y: 0};
        }
    },
    routes: [
		{
		    path: "/",
		    redirect: {
		        name: "index"
		    }
		},
        {
            path: "/index",
            name: "index",
            component: () =>
                import(/* webpackChunkName: "home" */ "./views/index/index.vue")
        },
		{
		    path: "/item",
		    name: "item",
		    component: () =>
		        import(/* webpackChunkName: "home" */ "./views/item/index.vue")
		},
		{
		    path: "/item/:code",
		    name: "itemDetail",
		    component: () =>
		        import(/* webpackChunkName: "home" */ "./views/item/detail.vue")
		},
		{
		    path: "/activity",
		    name: "activity",
		    component: () =>
		        import(/* webpackChunkName: "home" */ "./views/activity/index.vue")
		},
		{
		    path: "/activity/:code",
		    name: "activityDetail",
		    component: () =>
		        import(/* webpackChunkName: "home" */ "./views/activity/detail.vue")
		},
        {
            path: "/user",
            name: "user",
            component: () =>
                import(/* webpackChunkName: "home" */ "./views/user/index.vue")
        },
		{
		    path: "/account",
		    name: "account",
		    component: () =>
		        import(/* webpackChunkName: "home" */ "./views/user/account.vue")
		},
		{
		    path: "/addcount",
		    name: "addcount",
		    component: () =>
		        import(/* webpackChunkName: "home" */ "./views/user/addcount.vue")
		},
		{
		    path: "/addcountUsdt",
		    name: "addcountUsdt",
		    component: () =>
		        import(/* webpackChunkName: "home" */ "./views/user/addcountUsdt.vue")
		},
		{
		    path: "/moneybag",
		    name: "moneybag",
		    component: () =>
		        import(/* webpackChunkName: "home" */ "./views/user/moneybag.vue")
		},
		{
		    path: "/rechargeRecord",
		    name: "rechargeRecord",
		    component: () =>
		        import(/* webpackChunkName: "home" */ "./views/user/rechargeRecord.vue")
		},
		{
		    path: "/withdrawRecord",
		    name: "withdrawRecord",
		    component: () =>
		        import(/* webpackChunkName: "home" */ "./views/user/withdrawRecord.vue")
		},
        {
            path: "/login",
            name: "login",
            component: () =>
                import(/* webpackChunkName: "home" */ "./views/user/login.vue")
        },
        {
            path: "/register",
            name: "register",
            component: () =>
                import(/* webpackChunkName: "home" */ "./views/user/register.vue")
        },
        {
            path: "/forgetpwd",
            name: "forgetpwd",
            component: () =>
                import(/* webpackChunkName: "home" */ "./views/user/forgetpwd.vue")
        },
        {
            path: "/invest",
            name: "invest",
            component: () =>
                import(/* webpackChunkName: "home" */ "./views/user/invest.vue")
        },
        {
            path: "/invest/alipay",
            name: "pay",
            component: () =>
                import(/* webpackChunkName: "home" */ "./views/user/invest_qrcode.vue")
        },
        {
            path: "/invest/wx",
            name: "wx",
            component: () =>
                import(/* webpackChunkName: "home" */ "./views/user/invest_qrcode.vue")
        },
		{
		    path: "/invest/usdt",
		    name: "usdt",
		    component: () =>
		        import(/* webpackChunkName: "home" */ "./views/user/invest_qrcode.vue")
		},
        {
            path: "/invest/bank",
            name: "bankpay",
            component: () =>
                import(/* webpackChunkName: "home" */ "./views/user/invest_bank.vue")
        },
        {
            path: "/cash",
            name: "cash",
            component: () =>
                import(/* webpackChunkName: "home" */ "./views/user/cash.vue")
        },
        {
            path: "/safe",
            name: "safe",
            component: () =>
                import(/* webpackChunkName: "home" */ "./views/user/safe.vue")
        },
        {
            path: "/setpwd",
            name: "setpwd",
            component: () =>
                import(/* webpackChunkName: "home" */ "./views/user/setpwd.vue")
        },
        {
            path: "/setpaypwd",
            name: "setpaypwd",
            component: () =>
                import(/* webpackChunkName: "home" */ "./views/user/setpaypwd.vue")
        },
        {
            path: "/order",
            name: "order",
            component: () =>
                import(/* webpackChunkName: "home" */ "./views/user/order.vue")
        },
        {
            path: "/funds",
            name: "funds",
            component: () =>
                import(/* webpackChunkName: "home" */ "./views/user/funds.vue")
        },
		{
		    path: "/myTeam",
		    name: "myTeam",
		    component: () =>
		        import(/* webpackChunkName: "home" */ "./views/user/myTeam.vue")
		},
        {
            path: "/notice",
            name: "notice",
            component: () =>
                import(/* webpackChunkName: "home" */ "./views/user/notice.vue")
        },
        {
            path: "/notice/:id",
            name: "noticeid",
            component: () =>
                import(/* webpackChunkName: "home" */ "./views/user/notice_article.vue")
        },
        {
            path: "/about",
            name: "about",
            component: () =>
                import(/* webpackChunkName: "home" */ "./views/user/about.vue")
        },
        {
            path: "/about/:code",
            name: "article",
            component: () =>
                import(/* webpackChunkName: "home" */ "./views/user/article.vue")
        },
        
		{
		    path: "/search",
		    name: "search",
		    component: () =>
		        import(/* webpackChunkName: "home" */ "./views/item/search.vue")
		},
        {
            path: "/kefu",
            name: "kefu",
            component: () =>
                import(/* webpackChunkName: "home" */ "./views/user/kefu.vue")
        },
        {
            path: "/sign",
            name: "sign",
            component: () =>
                import(/* webpackChunkName: "home" */ "./views/user/sign.vue")
        },
		{
		    path: "/luckDraw",
		    name: "luckDraw",
		    component: () =>
		        import(/* webpackChunkName: "home" */ "./views/user/luckDraw.vue")
		},
		{
		    path: "/authEmail",
		    name: "authEmail",
		    component: () =>
		        import(/* webpackChunkName: "home" */ "./views/user/authEmail.vue")
		},
		
		{
		    path: "/language",
		    name: "language",
		    component: () =>
		        import(/* webpackChunkName: "home" */ "./views/user/language.vue")
		},
    ]
});
