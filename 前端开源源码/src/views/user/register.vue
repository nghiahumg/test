<template>
	<div class="basic_wrap">
		<div class="login_bg">
			<div class="back_left" @click="$router.back()" />
			<div class="big_tit">
				{{$t('register.register')}}
			</div>
		</div>
		<form class="form">
			<div class="cut_login">
				<router-link to="/login">
					{{$t('register.signIn')}}
					<div class="line" />
				</router-link>
				<router-link class="cur" to="/register">
					{{$t('register.signUp')}}
					<div class="line" />
				</router-link>
			</div>
			<div class="item">
				<span class="icon">
					<img src="../images/login/email.png" alt="">
				</span>
				<input v-model.trim="data.mobile" type="text" class="inp" :placeholder="$t('register.phoneNumber')">
			</div>
			<div class="item">
				<span class="icon">
					<img src="../images/login/tel.png" alt="">
				</span>
				<input v-model.trim="data.phone" type="text" class="inp" :placeholder="$t('register.phoneNumber2')">
			</div>
			<!-- <div class="item">
				<span class="icon">
					<img src="../images/login/p1.png" alt="">
				</span>
				<input type="text" class="inp" v-model.trim="data.name" placeholder="请输入用户名" />
			</div> -->
			<div class="item">
				<span class="icon">
					<img src="../images/login/lock.png" alt="">
				</span>
				<input v-model.trim="data.password" :type="password" class="inp" :placeholder="$t('register.password')">
				<div class="eye_bi" :class="password == 'text' ? 'eye' : ''" @click="showPwd" />
			</div>
			<!-- <div class="item">
        <span class="icon">
          <img
            src="../images/login/lock.png"
            alt=""
          >
        </span>
        <input
          v-model.trim="data.spassword"
          :type="password"
          class="inp"
          :placeholder="$t('register.passwordAgain')"
        >
        <div
          class="eye_bi"
          :class="password == 'text' ? 'eye' : ''"
          @click="showPwd"
        />
      </div> -->

			<!-- 			<div class="item">
				<input type="password" class="inp" v-model.trim="data.paypwd" placeholder="请输入支付密码" />
			</div>
			<div class="item">
				<input type="password" class="inp" v-model.trim="data.confirm_paypwd" placeholder="请再次输入支付密码" />
			</div> -->

			<div v-if="show_sms > 0" class="item">
				<input v-model.trim="data.code" type="text" class="inp" :placeholder="$t('register.code')">
				<van-count-down :time="time" class="btn get_captcha" @finish="timeCall">
					<template v-slot="timeData">
						<span @click="sendcode">{{
              timeData.seconds > 0
                ? timeData.seconds
                : $t('register.sendCode')
            }}</span>
					</template>
				</van-count-down>
			</div>
			<div class="item">
				<span class="icon">
					<img src="../images/login/p2.png" class="people" alt="">
				</span>
				<input v-model.trim="data.t_mobile" type="text" class="inp" :placeholder="$t('register.referralCode')">
			</div>
		</form>
		<div class="basic_btn sbtn" :class="
        data.mobile == '' ||
          data.password == '' 
		  ||data.phone == ''
          ? 'no_touch'
          : ''
      " @click="submit">
			{{$t('register.signUpBtn')}}
		</div>
		<van-dialog v-model="statement" confirm-button-color="#fb661e" :close-on-click-overlay="true"
			@click="privacyStatementHide">
		</van-dialog>
		<van-dialog v-model="agreement" confirm-button-color="#fb661e" :close-on-click-overlay="true"
			@click="agreementHider">
		</van-dialog>
		<div v-if="msg_show" class="kefu" :class="show_kefu ? '' : 'kefu_hide'" @click="kefu_to">
			<img class="kefu_img" src="../images/index/kefu.png">
		</div>
	</div>
</template>

<script>
	import Vue from "vue";
	import {
		CountDown,
		Checkbox,
		Dialog
	} from "vant";
	import Fetch from "../../utils/fetch";

	Vue.use(CountDown)
		.use(Checkbox)
		.use(Dialog);

	export default {
		name: "Register",
		data() {
			return {
				time: 0,
				agreement: false,
				statement: false,
				show_kefu: false,
				msg_show: true,
				show_sms: 0,
				password: "password",
				data: {
					password: "",
					spassword: "",
					mobile: "",
					t_mobile: "",
					code: "",
					agent: 10000,
					phone: ""
				},
				config: {}
			};
		},
		created() {
			this.$parent.footer("user", false);
			this.getparams();
			// 安卓返回IMEI，苹果返回UID值
			/*BSL.PhoneID('callName');
			window.callName = this.methodName;
			// 获取手机OAID
			BSL.GetOAID('getExternalInfo');
			window.getExternalInfo = this.getExternalInfoData; */
		},
		mounted() {
			if (this.$route.query.agent) {
				localStorage.setItem('agent', this.$route.query.agent);
				this.data.agent = this.$route.query.agent;
			} else {
				if (localStorage.getItem('agent')) {
					this.data.agent = localStorage.getItem('agent');
				}
			}
			var that = this;
			document.body.addEventListener(
				"scroll",
				function() {
					if (!that.show_kefu) {
						return;
					}
					that.show_kefu = false;
				},
				false
			);
			document.addEventListener(
				"click",
				function(ev) {
					if (ev.target.className != "kefu_img") {
						that.show_kefu = false;
					}
				},
				false
			);
			Fetch('/index/webconfig', {
				type: 'web'
			}).then(res => {
				this.config = res.data
				this.show_sms = res.data.sms_verify
			})
		},
		methods: {
			kefu_to() {
				if (this.show_kefu) {
					this.$router.push("/kefu");
				}
				this.show_kefu = !this.show_kefu;
			},
			showPwd() {
				if (this.password == "password") {
					this.password = "text";
				} else {
					this.password = "password";
				}
			},
			methodName(r) {
				this.data.imei = r;
			},
			getExternalInfoData(info) {
				var info = JSON.parse(info);
				console.log(info);
				if (info.code == 0) {
					this.data.oaid = info.oaid;
				}
			},
			getparams() {
				var m = this.$route.query.m;
				this.data.t_mobile = m;
			},
			timeCall() {
				this.is_send = false;
				this.time = 0;
			},
			sendcode() {
				if (this.is_send) {
					return;
				}

				if (!this.data.mobile) {
					this.$toast(this.$t('register.phoneEmpty'));
					return;
				}

				this.is_send = true;

				Fetch("/index/register_code", {
						mobile: this.data.mobile,
					})
					.then(() => {
						this.time = 60 * 1000;
						this.$toast({
							background: "#07c160",
							message: this.$t('register.sendSuccess'),
						});
					})
					.catch(() => {
						this.is_send = false;
					});
			},
			submit() {
				if (!this.data.mobile) {
					this.$toast(this.$t('register.phoneEmpty'));
					return false;
				}
				var reg = /^[0-9]*$/; //正则表达式
				if (!reg.test(this.data.phone)) {
					this.$toast(this.$t('register.phoneNumber2Error'));
					return false;
				}
				if (!this.data.password) {
					this.$toast(this.$t('register.passwordEmpty'));
					return false;
				}

				if (!this.data.code && this.config.sms_verify) {
					this.$toast(this.$t('register.codeEmpty'));
					return false;
				}
				// if (this.data.password != this.data.spassword) {
				//     this.$toast(this.$t('register.passwordError'));
				//     return false;
				// }

				Fetch("/index/register", {
					...this.data,
				}).then((res) => {
					if (res.data.token) {
						localStorage.setItem('token', res.data.token);
					}
					this.$toast(this.$t('register.signUpSuccess'));
					this.$router.replace("/");
				});
			},
			userAgreement() {
				this.agreement = true;
			},
			agreementHider() {
				this.agreement = false;
			},
			privacyStatement() {
				this.statement = true;
			},
			privacyStatementHide() {
				this.statement = false;
			},
		},
	};
</script>

<style lang="less" scoped>
	.login_bg {
		width: 100%;
		height: 178px;
		background: url(../images/login/login_bg.png) no-repeat center center;
		background-size: 100% 100%;
		overflow: hidden;
	}

	.basic_wrap .form {
		margin: -115px auto 0 auto;
		width: 90%;
		background: rgba(255, 255, 255, 1);
		box-shadow: 0px 4px 9px 2px rgba(160, 160, 160, 0.15);
		border-radius: 13px;
		padding: 31px 24px 31px 24px;
	}

	.cut_login {
		width: 207px;
		line-height: 25px;
		display: flex;
		align-items: center;
		justify-content: center;
		font-size: 18px;
		text-align: center;
		margin: 0 auto;

		a {
			width: 50%;
			padding-bottom: 10px;
			color: #ceced0;
			position: relative;

			.line {
				width: 100%;
				height: 1px;
				position: absolute;
				left: 0;
				bottom: 0;
				background-color: #f2f2f2;
			}
		}

		a.cur {
			color: #f12211;

			.line {
				height: 2px;
				background-image: linear-gradient(to right, #f12211, #f18a11);
			}
		}
	}

	.block {
		width: 100%;
		height: 100%;
		// overflow:hidden;
		// margin: 10% auto;
		font-size: 15px;
		justify-content: center;
		background-color: #fff;
		border-radius: 5px;

		.block-title {
			display: block;
			font-weight: bolder;
			line-height: 39px;
			height: 39px;
			text-align: center;
		}

		.p_agr {
			// padding-top:30rpx;
			font-size: 12px;
			/*font-weight: bolder;*/
			line-height: 20px;
			padding: 0px 10px;
			height: 388px;
			/*overflow: hidden;*/
			// overflow: auto;
			-webkit-overflow-scrolling: touch;
			overflow-y: scroll;
		}

		.div_qd {
			width: 60px;
			height: 25px;

			span {
				width: 60px;
				height: 20px;
				line-height: 20px;
				text-align: center;
				justify-content: center;
				background: #fb661e;
				border-radius: 10px;
				padding: 1px 5px;
				color: #fff;
			}

			margin: 10px auto;
		}
	}

	.item {
		height: 58px;
		border-bottom: 1px solid #f0f1f3;
		font-size: 14px;
		display: flex;
		justify-content: flex-start;
		align-items: flex-end;
		padding-bottom: 4px;

		.icon {
			display: flex;
			justify-content: center;
			align-items: center;
			width: 28px;
			height: 24px;

			.people {
				width: 23px;
				height: 20px;
			}

			img {
				width: 100%;
				height: 100%;
			}
		}

		input {
			height: 20px;
			margin-left: 7px;
			flex: 1;
			margin-bottom: 3px;
		}
	}

	.get_captcha {
		width: 88px;
		height: 34px;
		background: linear-gradient(90deg,
				rgba(228, 57, 46, 1) 0%,
				rgba(254, 76, 22, 1) 100%);
		border-radius: 4px;
		text-align: center;
		line-height: 34px;
		font-size: 14px;
		color: #ffffff;
	}

	.protocol {
		font-size: 12px;
		line-height: 17px;
		margin-top: 20px;
		color: #999999;

		a {
			color: #3d96ff;
		}
	}

	.basic_btn {
		margin: 36px auto 0 auto;
	}
</style>
