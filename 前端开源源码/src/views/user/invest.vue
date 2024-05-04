<template>
	<div class="recharge_wrap">
		<div class="red_top_bg">
			<div class="back_left" @click="$router.go(-1)"></div>
			<div class="big_tit">{{$t('invest.recharge')}}</div>
		</div>
		<div class="balance" v-if="eye==1">
			<p>
				{{$t('utils.moneyMark')}}{{balance}}
			</p>
			<p>
				{{$t('invest.availableBalance')}}
				<van-icon name="eye-o" @click="eye=0" />
			</p>
		</div>
		<div class="balance" v-if="eye==0">
			<p>
				******
			</p>
			<p>
				{{$t('invest.availableBalance')}}
				<van-icon name="closed-eye" @click="eye=1" />
			</p>
		</div>
		<form class="form">
			<div class="recharge_wrap">
				<p>{{$t('invest.rechargeAmount')}}</p>
				<div class="flex_center">
					<span>{{$t('utils.moneyMark')}}</span>
					<input v-model.trim="data.money" type="number" step="0.01" class="inp"
						:placeholder="$t('invest.rechargeAmount')" @input="changeAmount">
					<span>{{actualAmount}}</span>
				</div>
			</div>
			<div class="recharge_fs">
				<div class="tit">
					{{$t('invest.paymentMethod')}}
				</div>
				<div v-for="(v, k) in config.payment" :key="k" class="item" @click="choose_type(k+1,v)">
					<div class="check" :class="k+1 == data.choose? 'checked' : ''" />
					<div class="re_icon"><img :src="v.logo" /></div>
					<div class="re_name">
						{{ v.name }}
					</div>
				</div>
			</div>
		</form>
		<div class="basic_btn sbtn" @click="submit">
			{{$t('invest.next')}}
		</div>
	</div>
</template>

<script>
	import Vue from 'vue';
	import {
		CountDown,
		Checkbox,
		RadioGroup,
		Icon,
		Radio
	} from 'vant';
	import {
		jsBridge
	} from '../../utils/jsbridge-mini.js';
	import Fetch from '../../utils/fetch'
	import Api from "../../interface/index";

	Vue.use(RadioGroup);
	Vue.use(Radio);
	Vue.use(CountDown).use(Checkbox).use(Icon);

	export default {
		name: "invest",
		data() {
			return {
				time: 0,
				eye: 1,
				balance: 0.00,
				data: {
					choose: '',
					channel: '',
					money: '',
					id: ''
				},
				payment:[],
				config: {},
				actualAmount:'',
			};
		},
		created() {
			this.$parent.footer('user', false);
		},
		mounted() {
			this.start();
		},
		methods: {
			start() {
				Fetch('/user/recharge', {
					...this.data
				}).then((r) => {
					this.balance = r.data.money;
					this.config = r.data;
					this.config.min_recharge = parseInt(this.config.min_recharge);
					this.choose_type(1,r.data.payment[0]);
				});
			},
			checkLimit() {
				const key = 'invest_button';
				const ttl = 60;
				let now = Date.parse(new Date()) / 1000;
				let val = localStorage.getItem(key);
				if (!val) {
					val = JSON.stringify({
						'ttl': now + ttl
					})
					localStorage.setItem(key, val)
					return true;
				} else {
					val = JSON.parse(val);
					if (val['ttl'] > now) {
						return false;
					} else {
						val = JSON.stringify({
							'ttl': now + ttl
						})
						localStorage.setItem(key, val)
						return true;
					}
				}
			},
			choose_type(k, payment) {
				this.data.choose = k;
				this.data.channel = payment.type;
				this.data.id = payment.id;
				this.payment = payment;
				this.changeAmount();
			},
			submit() {
				if (!this.data.money || this.data.money < this.config.min_recharge) {
					this.$toast(this.$t('invest.rechargeAmountLow') + this.$t('utils.moneyMark') + this.config
						.min_recharge + this.$t('invest.rechargeAmountLow1'));
					return false;
				}
				if (!this.data.channel) {
					this.$toast(this.$t('invest.paymentMethodEmpty'));
					return false;
				}
				// if (!this.checkLimit()) {
				// 	this.$toast(this.$t('invest.overtop'));
				// 	return false;
				// }

				if (this.data.channel == 4) {
					this.$router.replace({
						path: '/invest/bank',
						query: {
							money: this.data.money,
							id: this.data.id
						}
					})
					return;
				} else if (this.data.channel == 2) {
					this.$router.replace({
						path: '/invest/alipay',
						query: {
							money: this.data.money,
							type: this.data.channel,
							id: this.data.id
						}
					})
					return;
				} else if (this.data.channel == 3) {
					this.$router.replace({
						path: '/invest/wx',
						query: {
							money: this.data.money,
							type: this.data.channel,
							id: this.data.id
						}
					})
					return;
				} else if (this.data.channel == 1) {
					this.$router.replace({
						path: '/invest/usdt',
						query: {
							money: this.data.money,
							id: this.data.id
						}
					})
					return;
				} else {
					this.$router.replace({
						path: '/invest/' + this.data.channel,
						query: {
							money: this.data.money,
						}
					});
				}
				// Fetch('/user/invest/apply', {
				//     ...this.data
				// }).then(() => {
				//     this.$router.replace('/login');
				// });
			},
			changeAmount() {
				var num = 2;
				if(this.payment.type){
					if (this.payment.type == 1) {
						if (this.payment.rate > 10000) {
							num = 8;
						} else if (this.payment.rate > 1000) {
							num = 6;
						} else if (this.payment.rate > 10) {
							num = 4;
						}
						this.actualAmount = "≈" + (this.data.money / this.payment.rate).toFixed(num) + this.payment.mark;
					} else {
						this.actualAmount = "≈" + this.payment.mark + (this.data.money / this.payment.rate).toFixed(num);
					}
				}
			}
		}
	};
</script>

<style lang="less" scoped>
	.red_top_bg {
		height: 126px;
		background: #3775f4;
		position: fixed;
	}

	.top_right {
		position: absolute;
		font-size: 13px;
		line-height: 18px;
		color: #FFFFFF;
		top: 36px;
		right: 14px;
	}

	.balance {
		width: 96%;
		margin-left: 2%;
		background: rgba(255, 255, 255, 1);
		border-radius: 6px;
		padding: 20px 0px;
		position: absolute;
		top: 78px;
		text-align: center;

		p:nth-child(1) {
			font-size: 26px;
			font-weight: bold;
			margin-bottom: 15px;
		}

		p:nth-child(2) {
			color: #999;
			font-weight: bold;
		}
	}

	.recharge_fs {
		margin-top: 10px;
		background: rgba(255, 255, 255, 1);
		border-radius: 6px;
		padding: 0 16px;
	}

	.recharge_box {
		width: 100%;
		height: 48px;
		background: rgba(255, 255, 255, 1);
		border-radius: 6px;
		margin-top: 12px;
		padding: 16px 11px;

		input {
			height: 18px;
			width: 100%;
			line-height: 18px;
			font-size: 14px;
		}
	}

	.form {
		width: 96%;
		margin-left: 2%;
		margin-top: 180px;

		.recharge_wrap {
			width: 100%;
			background: #ffffff;
			border-radius: 6px;
			padding: 16px 20px;
			margin-top: 10px;

			input {
				height: 18px;
				width: 60%;
				line-height: 18px;
				font-size: 14px;
			}

			div {
				span:nth-child(1){
					font-size: 18px;
					margin-right: 10px;
					font-weight: bold;
				}
				span:nth-child(3){
					font-size: 14px;
					width: 40%;
					color: #999;
					text-align: right;
				}
			}

			p:nth-child(1) {
				font-size: 16px;
				font-weight: bold;
				color: #999;
				margin-bottom: 20px;
			}

			p:nth-child(3) {
				color: #999;
				margin-top: 20px;
			}

		}

		.tit {
			height: 54px;
			border-bottom: 1px solid #ECECEC;
			font-size: 16px;
			color: #999;
			font-weight: bold;
			line-height: 54px;
		}

		.item {
			height: 57px;
			border-bottom: 1px solid #ECECEC;
			display: flex;
			justify-content: flex-start;
			align-items: center;

			&:last-child {
				border-bottom: none;
			}

			.check {
				width: 16px;
				height: 16px;
				margin-left: 4px;
				background: url(../images/user/c2.png) no-repeat center center;
				background-size: 100% 100%;

				&.checked {
					background: url(../images/user/c1.png) no-repeat center center;
					background-size: 100% 100%;
				}
			}

			.re_icon {
				width: 20px;
				height: 20px;
				margin-left: 11px;

				img {
					width: 20px;
					height: 20px;
				}
			}

			.re_name {
				font-size: 16px;
				line-height: 22px;
				color: rgba(0, 0, 0, .8);
				margin-left: 4px;
			}

			.wx {
				background: url(../images/user/recharge3.png) no-repeat center center;
				background-size: 100% 100%;
			}

			.alipay {
				background: url(../images/user/recharge2.png) no-repeat center center;
				background-size: 100% 100%;
			}

			.usdt {
				background: url(../images/user/recharge1.png) no-repeat center center;
				background-size: 100% 100%;
			}

			.alipay_app {
				background: url(../images/user/recharge2.png) no-repeat center center;
				background-size: 100% 100%;
			}

			.bank {
				background: url(../images/user/recharge4.png) no-repeat center center;
				background-size: 100% 100%;
			}
		}
	}

	.sbtn {
		margin: 75px auto 0 auto;
	}
</style>
