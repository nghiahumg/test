<template>
	<div class="home_wrap">
		<div class="red_top_bg">
			<div class="language" @click="$router.push('/language')">
				<img :src="require('../images/language/'+lang+'.png')">
			</div>
		    <div class="search_box" @click="$router.push('/search')">
		        <i class="fdj"></i>
		        <input type="text" readonly :placeholder="$t('index.search')" class="search_inp">
		        <div class="clear_inp"></div>
		    </div>
			<div class="msg" @click="$router.push('/notice')">
				<img src="../images/index/msg.png">
			</div>
		</div>
		<van-swipe :autoplay="2500" class="swiper-container" @change="onChange">
			<van-swipe-item v-for="(image, index) in data.banner" :key="index">
				<router-link :to="image.url">
					<img :src="image[lang]">
				</router-link>
			</van-swipe-item>
			<div ref="dian" slot="indicator" class="custom-indicator">
				<div v-for="(img, x) in data.banner" :key="x" :class="x===0?'selected':''" />
			</div>
		</van-swipe>
		<div v-show="loading" class="work_box">
			<div class="item" @click="$router.push('/sign')">
				<div class="item_img"><img src="../images/about/icon0.png" alt=""></div>
				<p class="item_title">{{$t('index.sign')}}</p>
			</div>
			<div class="item" @click="$router.push('/myTeam')">
				<div class="item_img"><img src="../images/about/icon8.png" alt=""></div>
				<p class="item_title">{{$t('index.invite')}}</p>
			</div>
			<div class="item" @click="$router.push('/invest')">
				<div class="item_img"><img src="../images/about/icon2.png" alt=""></div>
				<p class="item_title">{{$t('index.recharge')}}</p>
			</div>
			<div class="item" @click="$router.push('/cash')">
				<div class="item_img"><img src="../images/about/icon6.png" alt=""></div>
				<p class="item_title">{{$t('index.withdraw')}}</p>
			</div>
			<div class="item" @click="$router.push('/about/8')">
				<div class="item_img"><img src="../images/about/icon1.png" alt=""></div>
				<p class="item_title">{{$t('index.risk')}}</p>
			</div>
			<div class="item" @click="$router.push('/about/13')">
				<div class="item_img"><img src="../images/about/icon5.png" alt=""></div>
				<p class="item_title">{{$t('index.secret')}}</p>
			</div>
			<div class="item" @click="$router.push('/about/12')">
				<div class="item_img"><img src="../images/about/icon11.png" alt=""></div>
				<p class="item_title">{{$t('index.agreement')}}</p>
			</div>
			<div class="item" @click="$router.push('/about/11')">
				<div class="item_img"><img src="../images/index/help.png" alt=""></div>
				<p class="item_title">{{$t('index.aboutUs')}}</p>
			</div>
		</div>
		<div v-show="!loading" class="work_box query">
			<div v-for="i in 10" class="item">
				<div class="item_img" />
				<p class="item_title" />
			</div>
		</div>
		
		<div class="ads_box_1">
			<div class="ads_img" v-for="ad in data.ad1">
				<router-link :to="ad.url">
					<img :src="ad[lang]" alt="">
				</router-link>
			</div>
		</div>
		<div v-if="data.item1.show" class="items_1">
			<h5 class="items_tit">
				{{$t('item.tab1')}}
				<span class="tit_tip">{{$t('index.novice_tip')}}</span>
				<router-link to="/item?type=1">
					<span class="tit_more">{{$t('index.more')}}</span>
				</router-link>
			</h5>
			<div v-for="i in data.item1.list" :key="i.id" class="item">
				<div class="item_tit"  @click="$router.push('/item/'+i.id)">
					<p>
						{{ i[lang] }}
					</p>
				</div>
				<div class="item_rate" @click="$router.push('/item/'+i.id)">
					<p>{{i.rate}}%</p>
				</div>
				<div class="item_desc" @click="$router.push('/item/'+i.id)">
					<p>{{$t('utils.cycle')}}:{{i.day}}{{$t('utils.day')}}</p>
					<p>{{$t('utils.startingAmount')}}:{{$t('utils.moneyMark')}}{{i.min}}</p>
				</div>
				<div class="item_btn">
					<button v-if="i.percent<100" type="button" @click="buy(i)">{{$t('utils.invest')}}</button>
					<button v-if="i.percent>=100" class="item_btn_over" type="button">{{$t('utils.investOver')}}</button>
				</div>
			</div>
		</div>
		<div class="ads_box">
			<div class="ads_img" v-for="ad in data.ad2">
				<router-link :to="ad.url">
					<img :src="ad[lang]" alt="">
				</router-link>
			</div>
		</div>
		<div v-if="data.item2.show" class="items_2">
			<h5 class="items_tit">
				{{$t('item.tab2')}}
				<span class="tit_tip">{{$t('index.hot_tip')}}</span>
				<router-link to="/item?type=2">
					<span class="tit_more">{{$t('index.more')}}</span>
				</router-link>
			</h5>
			<div v-for="i in data.item2.list" :key="i.id" class="item">
				<div class="item_rate">
					<p>{{i.rate}}%</p>
					<p>{{$t('utils.dailyIncome')}}</p>
				</div>
				<div class="item_tit"  @click="$router.push('/item/'+i.id)">
					<p>
						{{ i[lang] }}
					</p>
					<p>{{ i['desc_'+lang] }}</p>
				</div>
				<div class="item_btn">
					<button v-if="i.percent<100" type="button" @click="buy(i)">{{$t('utils.invest')}}</button>
					<button v-if="i.percent>=100" class="item_btn_over" type="button">{{$t('utils.investOver')}}</button>
				</div>
			</div>
		</div>
		<div class="ads_box">
			<div class="ads_img" v-for="ad in data.ad3">
				<router-link :to="ad.url">
					<img :src="ad[lang]" alt="">
				</router-link>
			</div>
		</div>
		<div v-if="data.item3.show" class="items_3">
			<div v-for="i in data.item3.list" :key="i.id" class="item">
				<div class="item_div1"  @click="$router.push('/item/'+i.id)">
					<p>
						{{ i[lang] }}
					</p>
					<!-- <p>每日可赚100.00</p> -->
				</div>
				<div class="item_div2"  @click="$router.push('/item/'+i.id)">
					<div class="item_rate">
						<p>{{i.rate}}%</p>
						<p>{{$t('utils.dailyIncome')}}</p>
					</div>
					<div class="item_day">
						<p>{{i.day}}{{$t('utils.day')}}</p>
						<p>{{$t('utils.cycle')}}</p>
					</div>
					<div class="item_min">
						<p>{{$t('utils.moneyMark')}}{{i.min}}</p>
						<p>{{$t('utils.startingAmount')}}</p>
					</div>
				</div>
				<div class="item_div3">
					<span>{{$t('utils.scale')}}:{{$t('utils.moneyMark')}}{{i.total}}</span>
					<!-- <span>按日收益，到期还本</span> -->
				</div>
				<div class="item_div4">
					<van-progress :percentage="i.percent" />
					<div class="item_btn">
						<button v-if="i.percent<100" type="button" @click="buy(i)">{{$t('utils.invest')}}</button>
						<button v-if="i.percent>=100" class="item_btn_over" type="button">{{$t('utils.investOver')}}</button>
					</div>
				</div>
			</div>
		</div>
		<van-dialog 
		v-model="show_tz" 
		show-cancel-button
		@confirm="confirm"
		:confirmButtonText="$t('utils.confirm')"
		:cancelButtonText="$t('utils.cancel')"
		>
		<div class="items_3">
			<div class="item">
				<div class="item_div1">
					<p>
						{{ sku[lang] }}
					</p>
					<!-- <p>每日可赚100.00</p> -->
				</div>
				<div class="item_div2">
					<div class="item_rate">
						<p>{{sku.rate}}%</p>
						<p>{{$t('utils.dailyIncome')}}</p>
					</div>
					<div class="item_day">
						<p>{{sku.day}}{{$t('utils.day')}}</p>
						<p>{{$t('utils.cycle')}}</p>
					</div>
					<div class="item_min">
						<p>{{$t('utils.moneyMark')}}{{sku.min}}</p>
						<p>{{$t('utils.startingAmount')}}</p>
					</div>
				</div>
			</div>
		</div>
			<van-field v-model="number" type="number" :placeholder="$t('item.inputAmount')"/>
		</van-dialog>
		<van-dialog
		v-model="show_tc" 
		:confirmButtonText="$t('utils.confirm')"
		>
		<div class="popup" v-html="tc_content"></div>
		</van-dialog>
		<div v-if="msg_show" class="kefu" :class="show_kefu?'':'kefu_hide'" @click="kefu_to">
			<img class="kefu_img" src="../images/index/kefu.png">
		</div>
	</div>
</template>

<script>
	import Vue from 'vue';
	import Fetch from '../../utils/fetch';
	import Api from "../../interface/index"
	
	import {
		Swipe,
		SwipeItem,
		Icon,
		Progress,
		Dialog,
		Field
	} from "vant";

	Vue.use(Icon).use(Progress).use(Dialog).use(Field);

	export default {
		name: "Index",
		components: {
			VanSwipe: Swipe,
			VanSwipeItem: SwipeItem,
		},
		data() {
			return {
				lang:this.$i18n.locale||"zh_cn",
				show_kefu: false,
				msg_show: true,
				loading: false,
				icon: [],
				data: {},
				show_img: false,
				show_tz:false,
				show_tc:false,
				tc_content:"",
				number: '',
				sku:[]
			};
		},
		created() {
			this.$parent.footer('index');
		},
		mounted() {
			this.start();
			//代理标识设置
			if(this.$router.history.current.query.agent){
				localStorage.setItem('agent', this.$router.history.current.query.agent);
			}
			console.log();
			var that = this;
			document.body.addEventListener("scroll", function() {
				if (!that.show_kefu) {
					return;
				}
				that.show_kefu = false;
			}, false);
			document.addEventListener('click', function(ev) {
				if (ev.target.className != 'kefu_img') {
					that.show_kefu = false;
				}
			}, false);
		},
		methods: {
			kefu_to() {
				if (this.show_kefu) {
					this.$router.push('/kefu');
				}
				this.show_kefu = !this.show_kefu;
			},
			hideDown() {
				this.show_img = false;
				this.$store.commit('setShowAdvert', false);
			},
			start() {
				Fetch('/index/int').then((r) => {
					//打包web请注释掉版本更新这段代码
					//-----------------------------------------
					// if(window.plus){
					// let appVersion1 = plus.runtime.version;
					// let appVersion2 = r.data.version.app_version;
					// //版本号不同则更新APP
					// if(parseInt(appVersion1.split(".").join(""))<parseInt(appVersion2.split(".").join(""))){
					// 	this.$dialog.alert({
					// 	  title: 'APP更新',
					// 	  message: r.data.version.app_instructions,
					// 	}).then(() => {
					// 		let u = navigator.userAgent;
					// 		let isAndroid = u.indexOf("Android") > -1 || u.indexOf("Linux") > -1;
					// 		let isIOS = !!u.match(/\(i[^;]+;( U;)? CPU.+Mac OS X/);
					// 		if (isAndroid) {
					// 		 // alert("我是安卓");
					// 			window.location.href = r.data.version.android_app_down_url;
					// 		}
					// 		if (isIOS) {
					// 		 // alert("我是苹果");
					// 		 window.location.href = r.data.version.ios_app_down_url;
					// 		}
					// 	});
					// }
					// }
					//-----------------------------------------
					this.data = r.data;
					this.loading = true;
					if(r.data.popup.show==1){
						this.tc_content = r.data.popup.content;
						this.show_tc = true;
					}
				});
			},
			onChange(index) {
				let els = this.$refs.dian.querySelectorAll("div");
				for (let i = 0; i < els.length; i++) {
					els[i].className = "";
				}
				els[index].className = "selected";
			},
			buy(item){
				this.sku = item;
				this.show_tz = true;
			},
			confirm(){
				if(!this.number){
					this.$toast(this.$t('item.inputAmount'));
					return false;
				}
				Fetch("/index/item_apply", {
				  id: this.sku.id,
				  money:this.number
				}).then(r => {
				  this.$router.push("/order");
				}).catch(err=>{
				    this.psd_val = '';
				})
			}
		}
	};
</script>

<style lang="less" scoped>
	/deep/ .van-cell{
		padding: 15px 26px;
	}
	.home_wrap {
		width: 100%;
		min-height: 100vh;
		background-color: #F6F6F6;
	}
	.red_top_bg {
	    position: fixed;
	    top: 0;
	    z-index: 10;
		.language{
			position: absolute;
			top: 4px;
			left: 0;
			height: 36px;
			img{
				height: 100%;
			}
		}
		.msg{
			position: absolute;
			top: 9px;
			right: 6px;
			height: 23px;
			img{
				height: 100%;
			}
		}
	}
    .search_box {
        width: 80%;
        height: 29px;
        background-color: #f6f6f6;
        border-radius: 17px;
        margin: 8px 40px 0 40px;
        display: flex;
        justify-content: flex-start;
        align-items: center;
        padding: 0 10px 0 14px;

        .fdj {
            width: 16px;
            height: 16px;
            background: url(../images/item/fdj.png) no-repeat center center;
            background-size: 100% 100%;
        }

        .search_inp {
            flex: 1;
            margin: 0 8px;
            font-size: 14px;
            color: #000000;
            line-height: 20px;
            height: 20px;
        }

        .clear_inp {
            width: 20px;
            height: 20px;
            background: url(../images/item/clear.png) no-repeat center center;
            background-size: 100% 100%;
        }
    }

	.swiper-container {
		width: 100%;
		height: 154px;
		margin-top: 44px;
		background: #FFFFFF;

		.van-swipe-item {
			overflow: hidden;
		}

		a {
			float: left;
			width: 100%;
			height: 100%;
		}

		img {
			border-radius: 6px;
			margin-left: 2%;
			height: 100%;
			width: 96%;
		}
	}
	.work_box {
		background: #FFFFFF;
		padding: 17px 8px 4px 8px;
		display: flex;
		flex-wrap: wrap;
		justify-content: space-between;
		.item {
			width: 25%;
			text-align: center;
			margin-bottom: 10px;
			.item_img {
				width: 100%;
				border-radius: 5px;
				overflow: hidden;
				img {
					width: 44px;
					height: 44px;
				}
			}

			.item_title {
				width: 100%;
				line-height: 14px;
				font-size: 12px;
				color: #333333;
				text-align: center;
				transform: scale(0.85);
			}
		}
	}

	.work_box.query {
		.item {

			.item_img {
				border-radius: 5px;
				background: #f0f0f0;
			}

			.item_title {
				width: 100%;
				height: 14px;
				margin-left: 0;
				background: #f0f0f0;
			}
		}

	}
	.ads_box_1{
		border-radius: 13px;
		margin-top: 10px;
		text-align: center;
		img{
			width: 95%;
		}
	}
	.ads_box{
		// background: rgba(255, 255, 255, 1);
		width: 97%;
		border-radius: 13px;
		margin: 10px auto 0 auto;
		display: flex;
		text-align: center;
		.ads_img{
				
		}
		img{
			width: 96%;
			border-radius: 5px;
		}
	}
	.items_1,
	.items_2,
	.items_3
	{
		margin: 10px 2% 0 2%;
		width: 96%;
		.items_tit {
			border-radius: 5px 5px 0 0 ;
			height: 44px;
			line-height: 44px;
			font-size: 16px;
			color: rgba(0, 0, 0, .8);
			background: #FFFFFF;
			border-bottom: 1px solid #F7F7F7;
			padding: 0 14px;
			display: flex;
			justify-content: space-between;
			.tit_tip{
				display: contents;
				font-size: 12px;
				font-weight: normal;
				color: #b2b2b2;
				margin-left: 10px;
			}
			.tit_more{
				font-size: 12px;
				font-weight: normal;
				color: #b2b2b2;
				
			}
		}
		.item{
			background: #FFFFFF;
		}
	}
	.items_1 {
		.item{
			padding: 5px;
			margin: 0 0 10px 0;
			div{
				margin: 20px 0;
			}
			.item_tit{
				text-align: center;
				overflow: hidden;
				p{
					text-overflow:ellipsis;
					white-space: nowrap;
					font-weight: bold;
					font-size: 16px;
				}
			}
			.item_rate{
				text-align: center;
				color: #FF0000;
				font-size: 22px;
				font-weight: bold;
			}
			.item_desc{
				display: flex;
				p{
					width: 50%;
					text-align: center;
				}
			}
			.item_btn{
				text-align: center;
				
				button{
					width: 60%;
					min-width: 60px;
					background: #e7544d;
					color: #FFFFFF;
					padding: 10px 0px;
					border-radius: 10px;
				}
				.item_btn_over{
					background: #999;
				}
			}
		}
	}
	.items_2 {
		.item{
			display: flex;
			width: 100%;
			text-align: center;
			border-bottom: 1px solid #ECECEC;
			div{
				margin: 15px 0;
			}
			.item_rate{
				width: 20%;
				p{
					padding: 5px 0;
				}
				p:nth-child(1){
					font-weight: bold;
					font-size: 14px;
					color: #FF0000;
					
				}
				p:nth-child(2){
					color: #999;
				}
			}
			.item_tit{
				width: 55%;
				p{
					overflow:hidden;
					text-overflow:ellipsis;
					white-space:nowrap;
					padding: 5px 0;
				}
				p:nth-child(1){
					font-weight: bold;
				}
				p:nth-child(2){
					color: #999;
				}
			}
			.item_btn{
				width: 25%;
				button{
					min-width: 60px;
					margin-top: 12px;
					color: #1989fa;
					padding: 5px 5px;
					border-radius: 5px;
					border: 1px solid #1989fa;
				}
				.item_btn_over{
					color: #999;
					border: 1px solid #999;
				}
			}
		}
	}
	.items_3 {
		.item{
			margin-bottom: 10px;
			div{
				margin: 5px 0;
			}
			.item_div1{
				display: flex;
				justify-content: space-between;
				border-bottom: 1px solid #ECECEC;
				padding: 15px 10px;
				p:nth-child(1){
					// width: 70%;
					font-weight: bold;
					overflow:hidden;
					text-overflow:ellipsis;
					white-space:nowrap;
					
				}
				p:nth-child(2){
					color: #999;
				}
			}
			.item_div2{
				display: flex;
				justify-content: space-between;
				text-align:center;
				font-size: 14px;
				div{
					width: 33.33%;
					p{
						padding: 5px 0;
					}
				}
				.item_rate{
					p:nth-child(1){
						font-weight: bold;
						color: #FF0000;
						
					}
					p:nth-child(2){
						color: #999;
					}
				}
				.item_day{
					p:nth-child(1){
						font-weight: bold;
						
					}
					p:nth-child(2){
						color: #999;
					}
				}
				.item_min{
					p:nth-child(1){
						font-weight: bold;
						
					}
					p:nth-child(2){
						color: #999;
					}
				}
			}
			.item_div3{
				color: #999;
				padding: 5px 10px;
			}
			.item_div4{
				padding: 5px 10px;
				display: flex;
				justify-content: space-between;
				div:nth-child(1){
					width: 75%;
					margin-top: 16px;
				}
				.item_btn{
					width: 20%;
					button{
						min-width: 60px;
						color: #1989fa;
						padding: 5px 5px;
						border-radius: 5px;
						border: 1px solid #1989fa;
					}
					.item_btn_over{
						color: #999;
						border: 1px solid #999;
					}
				}
			}
		}
	}
	.popup{
		-webkit-box-flex: 1;
		-webkit-flex: 1;
		flex: 1;
		max-height: 60vh;
		padding: 26px 24px;
		overflow-y: auto;
		font-size: 14px;
		line-height: 20px;
	}
</style>
