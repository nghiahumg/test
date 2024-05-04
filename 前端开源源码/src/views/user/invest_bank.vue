<template>
	<div class="bankrecharge_wrap">
		<div class="red_top_bg">
			<div class="back_left" @click="$router.push('/user')"></div>
			<div class="big_tit">{{ data.payment.bank }}</div>
		</div>
		<div class="bank_detail">
			<div class="item">
				<p class="info">
				{{$t('invest_qrcode.amount')}}
				<span>{{$t('utils.moneyMark')}}{{money}}</span>
				<span>{{actualAmount}}</span>
				</p>
			</div>
			<div class="item">
				<p class="info">{{$t('invest_bank.name')}}
				<span>{{ data.payment.username }}</span></p>
				<div class="copy_btn" v-clipboard="() => data.payment.username " v-clipboard:success="copy">
					{{$t('invest_bank.copy')}}
				</div>
			</div>
			<div class="item">
				<p class="info">{{$t('invest_bank.account')}}
				<span>{{ data.payment.account }}</span></p>
				<div class="copy_btn" v-clipboard="() => data.payment.account " v-clipboard:success="copy">
					{{$t('invest_bank.copy')}}
				</div>
			</div>
			<div class="item">
				<p class="info">{{$t('invest_bank.bank')}}
				<span>{{ data.payment.bank }}</span></p>
				<div class="copy_btn" v-clipboard="() => data.payment.bank " v-clipboard:success="copy">
					{{$t('invest_bank.copy')}}
				</div>
			</div>
			<!-- <div class="item">
				<p class="info">付款人：<input type="text" v-model.trim="pay_user" placeholder="请输入付款人"></p>
			</div>
			<div class="item">
				<p class="info">附言：<input type="text" v-model.trim="pay_remark" placeholder="请输入充值附言"></p>
			</div> -->
		</div>
		<div class="basic_btn" @click="submitBtn">{{$t('utils.submit')}}</div>
	</div>
</template>

<script>
	import Vue from "vue";
	import {
		Button
	} from "vant";
	import Fetch from "../../utils/fetch";
	import Clipboard from "v-clipboard";

	Vue.use(Button).use(Clipboard);

	export default {
		name: "invest_bank",
		data() {
			return {
				data: {},
				pay_user: '',
				pay_remark: '',
				money: 0,
				actualAmount:'',
			};
		},
		created() {
			this.$parent.footer("user", false);
		},
		mounted() {
			this.money = this.$route.query.money ? this.$route.query.money : "";
			this.start();
		},
		methods: {
			copy() {
				this.$toast({
					background: "#07c160",
					message: this.$t('invest_qrcode.copySucceeded'),
				});
			},
			start() {
				Fetch("/user/recharge_type", {
					money: this.$route.query.money ? this.$route.query.money : "",
					id:this.$route.query.id ? this.$route.query.id : ""
				}).then((r) => {
					this.data = r.data;
					this.changeAmount();
				});
			},
			submitBtn() {
				Fetch("/user/recharge_apply", {
					id: this.data.orderId
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
					this.actualAmount = "(≈" + (this.money / this.data.payment.rate).toFixed(num) + this.data.payment.mark+")";
				} else {
					this.actualAmount = "(≈" + this.data.payment.mark + (this.money / this.data.payment.rate).toFixed(num)+")";
				}
			}
		},
	};
</script>

<style lang="less" scoped>
	.bankrecharge_wrap {
		background: #fd1c5f !important;
		width: 100%;
		height: 100%;
		position: fixed;
	}
	.red_top_bg{
		background: #fd1c5f;
	}
	.bank_detail {
		width: 96%;
		margin-left: 2%;
		background: rgba(255, 255, 255, 1);
		box-shadow: 0px 2px 9px 2px rgba(160, 160, 160, 0.15);
		border-radius: 6px;
		margin-top: 12px;
		padding: 30px 16px;
		font-size: 14px;

		.item {
			height: 52px;
			border-bottom: 1px solid #ececec;
			line-height: 52px;
			display: flex;
			justify-content: space-between;
			align-items: center;

			.info {
				font-weight: bold;
				color: #000;
				span:nth-child(1){
					font-weight: initial;
				}
				span:nth-child(2){
					color: #999;
					font-size: 14px;
				}
			}

			.copy_btn {
				color: #3d96ff;
				font-size: 12px;
			}
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
