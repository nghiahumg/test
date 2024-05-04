<template>
	<div class="usdt_wrap">
		<div class="red_top_bg">
			<div class="back_left" @click="$router.push('/user')"></div>
			<div class="big_tit">{{data.payment.name}}</div>
		</div>
		<div class="code_box" style="margin-top: 70px;">
			<div class="money">{{$t('utils.moneyMark')}}{{money}}
				<span>{{actualAmount}}</span>
			</div>
			<div v-if="data.payment.type==1">{{$t('invest_qrcode.scanQRcode')}}</div>
			<div class="qrcode_img">
				<img class="qrcode" :src="data.payment.qrcode" alt="" />
			</div>
			<div class="wallet" v-if="data.payment.type==1">{{$t('invest_qrcode.walletAddress')}}</div>
			<div class="walletAddress" v-if="data.payment.type==1">{{data.payment.address}}</div>
		</div>
		<div class="button_box" v-if="data.payment.type==1">
			<div class="copy_link" @click="copyAddress" v-clipboard="() => data.payment.address"
				v-clipboard:success="copy">{{$t('invest_qrcode.copyWalletAddress')}}</div>
		</div>
		<div class="code_box" v-if="data.payment.type==1">
			<div class="inp_tips">{{$t('invest_qrcode.walletAddress')}}</div>
			<input v-model.trim="address" class="inp" maxlength="40" onkeyup="value=value.replace(/[\W]/g,'')"
				:placeholder="$t('invest_qrcode.inputAddress')">
		</div>
		<div class="basic_btn" @click="submitBtn">{{$t('utils.submit')}}</div>
	</div>
</template>

<script>
	import Vue from 'vue';
	import Fetch from '../../utils/fetch'
	import Api from "../../interface/index";
	import bsHeader from '../../components/bsHeader.vue'
	import Clipboard from 'v-clipboard';
	Vue.use(Clipboard);
	export default {
		name: "invest_qrcode",
		data() {
			return {
				data: {},
				money: 0,
				actualAmount: '',
				address: ''
			};
		},
		created() {
			this.$parent.footer('user', false);
		},
		mounted() {
			this.money = this.$route.query.money;
			this.start();
		},
		methods: {
			start() {
				Fetch('/user/recharge_type', {
					money: this.$route.query.money ? this.$route.query.money : "",
					id: this.$route.query.id ? this.$route.query.id : ""
				}).then((r) => {
					this.data = r.data;
					this.changeAmount();
				});
			},
			submitBtn() {
				if (!this.address) {
					this.$toast(this.$t('invest_qrcode.inputAddress'));
					return false;
				}
				Fetch("/user/recharge_apply", {
					id: this.data.orderId,
					address: this.address
				}).then((r) => {
					if (r.code == 1) {
						this.$toast({
							background: "#07c160",
							message: this.$t('invest_qrcode.submittedSuccessfully'),
						});
						this.$router.replace("/moneybag");
					}
				});
			},
			copyAddress() {
				this.mask_show = true;
				var that = this;
				var local = window.location.origin;
				var url = local + this.data.down_image;
			},
			copy() {
				this.$toast({
					background: '#07c160',
					message: this.$t('invest_qrcode.copySucceeded')
				})
			},
			changeAmount() {
				var num = 2;
				if (this.data.payment.type == 1) {
					if (this.data.payment.rate > 10000) {
						num = 8;
					} else if (this.data.payment.rate > 1000) {
						num = 6;
					} else if (this.data.payment.rate > 10) {
						num = 4;
					}
					this.actualAmount = "(≈" + (this.money / this.data.payment.rate).toFixed(num) + this.data.payment
						.mark + ")";
				} else {
					this.actualAmount = "(≈" + this.data.payment.mark + (this.money / this.data.payment.rate).toFixed(
						num) + ")";
				}
			}
		}
	};
</script>

<style lang="less" scoped>
	.usdt_wrap {
		background: #fd1c5f !important;
		width: 100%;
		position: absolute;
		padding-bottom: 100px;
	}

	.red_top_bg {
		background: #fd1c5f;
		position: fixed;
	}

	.button_box {
		width: 90%;
		margin-left: 5%;
		height: 60px;
		background: #ebedf0;
		box-shadow: 0px 2px 9px 2px rgba(160, 160, 160, 0.15);
		border-radius: 6px;
		margin-top: -10px;
		display: flex;
		text-align: center;

		.copy_link {
			width: 100%;
			line-height: 60px;
		}
	}

	.code_box {
		width: 90%;
		margin-left: 5%;
		padding: 30px 0;
		background: rgba(255, 255, 255, 1);
		box-shadow: 0px 2px 9px 2px rgba(160, 160, 160, 0.15);
		border-radius: 6px;
		margin-top: 20px;
		display: flex;
		flex-direction: column;
		justify-content: center;
		align-items: center;

		.money {
			color: #000;
			font-size: 26px;

			span {
				font-size: 14px;
				color: #999;
			}
		}

		.wallet {
			margin-top: 10px;
		}

		.walletAddress {
			text-align: center;
			color: #000;
			width: 80%;
		}

		div {
			color: #999;
			font-size: 14px;
			margin-bottom: 15px;
		}

		.qrcode_img {
			width: 200px;
			height: 200px;
			margin: 0 auto;
			text-align: center;
			background: linear-gradient(to left, #999, #999) left top no-repeat,
				linear-gradient(to bottom, #999, #999) left top no-repeat,
				linear-gradient(to left, #999, #999) right top no-repeat,
				linear-gradient(to bottom, #999, #999) right top no-repeat,
				linear-gradient(to left, #999, #999) left bottom no-repeat,
				linear-gradient(to bottom, #999, #999) left bottom no-repeat,
				linear-gradient(to left, #999, #999) right bottom no-repeat,
				linear-gradient(to left, #999, #999) right bottom no-repeat;
			background-size: 1px 20px, 20px 1px, 1px 20px, 20px 1px;

			span {
				line-height: 220px;
			}

			img {
				width: 190px;
				height: 190px;
				margin-top: 5px;
			}
		}

		p {
			font-size: 15px;
			line-height: 21px;
			color: #000;
			font-weight: bold;
			margin-top: 13px;
		}

		.address_left {
			width: 90%;
			text-align: left;
		}
		.inp_tips{
			width: 86%;
			font-weight: bold;
		}
		.inp {
			height: 18px;
			width: 86%;
			line-height: 18px;
			font-size: 14px;
		}
	}

	.warming {
		margin-top: 17px;
		padding: 0 14px;

		.tit {
			font-size: 14px;
			color: rgba(0, 0, 0, .8);
			line-height: 20px;
			font-weight: bold;
		}

		.content {
			font-size: 12px;
			color: rgba(0, 0, 0, .6);
			line-height: 17px;
			margin-top: 9px;
		}
	}

	.basic_btn {
		margin: 30px auto 0 auto;
		background: linear-gradient(90deg, #672ee4 0%, #16b4ff 100%);
	}
</style>
