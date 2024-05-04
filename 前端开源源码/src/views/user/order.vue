<template>
	<div class="basic_wrap">
		<div class="red_top_bg">
			<div class="back_left" @click="$router.back()"></div>
			<div class="big_tit">{{$t('user.myInvestment')}}</div>
			<div class="money_box">
				<div class="item">
					<p class="money_num" v-html="'<span>'+$t('utils.moneyMark')+'</span>'+data.on_money"></p>
					<p class="info">{{$t('user.uncollectedPrincipal')}}</p>
				</div>
				<div class="item">
					<p class="money_num" v-html="'<span>'+$t('utils.moneyMark')+'</span>'+data.on_apr_money"></p>
					<p class="info">{{$t('user.uncollectedInterest')}}</p>
				</div>
				<div class="item">
					<p class="money_num" v-html="'<span>'+$t('utils.moneyMark')+'</span>'+data.ok_apr_money"></p>
					<p class="info">{{$t('user.cumulativeIncome')}}</p>
				</div>
			</div>
		</div>
		<h3 class="notes_tit">{{$t('orders.record')}}</h3>
		<van-empty :description="$t('orders.noOrders')" v-if="empty" />
		<div class="scrollBox">
			<div class="notes_box" v-if="!empty" v-for="i in data.list">
				<p class="timer">
					{{ i.time }}
					<span class="ing" v-if="i.status==0">{{$t('orders.unfinish')}}</span>
					<span class="finish" v-if="i.status==1">{{$t('orders.finish')}}</span>
				</p>
				<div class="item_div1">
					<p>
						{{i[lang]}}
					</p>
				</div>
				<div class="item_div2">
					<div class="item_min">
						<p>{{$t('utils.moneyMark')}}{{i.money}}</p>
						<p>{{$t('utils.investmentAmount')}}</p>
					</div>
					<div class="item_rate">
						<p>{{$t('utils.moneyMark')}}{{(i.money*i.rate/100*i.day).toFixed(2)}}</p>
						<p>{{$t('utils.income')}}</p>
					</div>
					<div class="item_day">
						<p>{{i.day}}{{$t('utils.day')}}</p>
						<p>{{$t('utils.cycle')}}</p>
					</div>
				</div>
			</div>
		</div>
	</div>
</template>

<script>
	import Vue from "vue";
	import {
		Empty
	} from "vant";

	import Fetch from "../../utils/fetch";
	Vue.use(Empty);

	export default {
		name: "order",
		data() {
			return {
				lang: this.$i18n.locale || "zh_cn",
				data: {},
				empty: false,
			};
		},
		created() {
			this.$parent.footer("user", false);
		},
		mounted() {
			this.start();
		},
		methods: {
			start() {
				Fetch("/user/order").then((r) => {
					this.data = r.data;
					r.data.list.length == 0 ?
						(this.empty = true) :
						(this.empty = false);
				});
			},
			wan(str) {
				var num = parseFloat(str);
				if (num > 99999) {
					return `${(num / 10000).toFixed(2)}万`;
				} else {
					return str || 0;
				}
			},
			lookHT(id) {
				this.$router.push("/contract/" + id);
			},
		},
	};
</script>

<style lang="less" scoped>
	.red_top_bg {
		height: 156px;
		background: #3775f4;
		background-size: 100% 100%;
		position: fixed;
		z-index: 15;
	}

	.money_box {
		margin-top: 29px;
		display: flex;
		padding: 0 6px;
		justify-content: space-around;
		color: #ffffff;

		.item {
			width: 33.33%;
			text-align: center;
			font-weight: bold;

			.money_num {
				font-size: 18px;
				line-height: 25px;
				white-space: normal;
				word-break: normal;
				text-align: center;
			}

			.info {
				font-size: 14px;
				line-height: 20px;
				margin-top: 5px;
			}
		}
	}

	.notes_tit {
		width: 100%;
		line-height: 44px;
		height: 44px;
		font-size: 14px;
		color: #000000;
		padding: 0 14px;
		position: fixed;
		top: 156px;
		left: 50%;
		transform: translateX(-50%);
		background: #FAFAFA;
		z-index: 2;
	}

	.notes_box {
		width: 100%;
		padding-bottom: 2px;
		background: rgba(255, 255, 255, 1);
		box-shadow: 0px 2px 9px 2px rgba(160, 160, 160, 0.15);
		border-radius: 6px;
		margin-bottom: 10px;

		.timer {
			padding: 12px 14px;
			font-size: 12px;
			color: rgba(0, 0, 0, .6);
			border-bottom: 1px solid #ececec;
			display: flex;
			justify-content: space-between;
			align-items: center;

			span {
				font-style: normal;
				padding: 5px;
				border-radius: 5px;
				color: #FFFFFF;
			}

			.ing {
				background: #3775f4;
			}

			.finish {
				font-style: normal;
				background: #07c160;
			}
		}

		.item_div1 {
			display: flex;
			justify-content: space-between;
			padding: 15px 10px;

			p:nth-child(1) {
				width: 100%;
				font-weight: bold;
				overflow: hidden;
				text-overflow: ellipsis;
				white-space: nowrap;

			}
		}

		.item_div2 {
			display: flex;
			justify-content: space-between;
			text-align: center;
			margin-bottom: 10px;

			div {
				width: 33.33%;

				p {
					padding: 5px 0;
				}
			}

			.item_rate {
				p:nth-child(1) {
					font-weight: bold;
					font-size: 14px;
					color: #FF0000;

				}

				p:nth-child(2) {
					color: #999;
				}
			}

			.item_day {
				p:nth-child(1) {
					font-weight: bold;
					font-size: 14px;

				}

				p:nth-child(2) {
					color: #999;
				}
			}

			.item_min {
				p:nth-child(1) {
					font-weight: bold;
					font-size: 14px;

				}

				p:nth-child(2) {
					color: #999;
				}
			}
		}
	}

	.scrollBox {
		margin-top: 200px;
		width: 96%;
		margin-left: 2%;
	}
</style>
