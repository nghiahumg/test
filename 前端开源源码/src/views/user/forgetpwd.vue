<template>
    <div class="resetpwd_wrap">
        <div class="red_top_bg">
            <div class="back_left" @click="$router.back()"></div>
            <div class="big_tit">{{$t('forgetpwe.reset')}}</div>
        </div>
        <form class="form" @submit.prevent="submit">
            <div class="item">
                <input type="text" class="inp" v-model.trim="data.mobile" :placeholder="$t('forgetpwe.phoneNumber')"/>
            </div>
            <div class="item">
                <input type="text" class="inp" v-model.trim="data.code" :placeholder="$t('forgetpwe.code')"/>
                <van-count-down :time="time" class="btn get_captcha" @finish="timeCall">
                    <template v-slot="timeData">
                        <span @click="sendcode">{{timeData.seconds > 0 ? timeData.seconds : $t('forgetpwe.send')}}</span>
                    </template>
                </van-count-down>
            </div>
            <div class="item">
                <input type="password" class="inp" v-model.trim="data.password" :placeholder="$t('forgetpwe.newPassword')"/>
            </div>
            <button type="submit" class="btn basic_btn">{{$t('forgetpwe.submit')}}</button>
        </form>
    </div>
</template>

<script>

    import Vue from 'vue';
    import {CountDown, Checkbox} from 'vant';
    import Fetch from '../../utils/fetch'

    Vue.use(CountDown).use(Checkbox);

    export default {
        name: "register",
        data() {
            return {
                time: 0,
                data: {
                    mobile: '',
                    code: '',
                    password: '',
                }
            };
        },
        created() {
            this.$parent.footer('user',false);
        },
        mounted() {
        },
        methods: {
            timeCall() {
                this.is_send = false;
                this.time = 0;
            },
            sendcode() {

                if (this.is_send) {
                    return;
                }

                if (!this.data.mobile) {
                    this.$toast(this.$t('forgetpwe.phoneNumberEmpty'));
                    return;
                }

                this.is_send = true;

                Fetch("/index/forgetpwd_code", {
                    mobile: this.data.mobile,
					lang: this.$i18n.locale||"zh_cn"
                }).then(() => {
                    this.time = 60 * 1000;
                    this.$toast({
                        background: '#07c160',
                        message: this.$t('forgetpwe.sendSuccess')
                    });
                }).catch(() => {
                    this.is_send = false;
                });

            },
            submit() {
                if (!this.data.mobile) {
                    this.$toast(this.$t('forgetpwe.phoneNumberEmpty'));
                    return;
                }

                if (!this.data.password) {
                    this.$toast(this.$t('forgetpwe.newPasswordEmpty'));
                    return;
                }

                if (!this.data.code) {
                    this.$toast(this.$t('forgetpwe.codeEmpty'));
                    return;
                }

                Fetch("/index/forgetpwd_nologin", {
                    mobile: this.data.mobile,
                    password: this.data.password,
                    code: this.data.code
                }).then(() => {
                    this.$toast({
                        background: '#07c160',
                        message: this.$t('forgetpwe.success')
                    });
                }).then(() => {
                    this.$router.replace('/login');
                })
            },
        }
    };
</script>

<style lang="less" scoped>
	.back_left {
		background: url(../../views/images/item/back_b.png) no-repeat center center;
	}
	.big_tit{
		color: #000000;
	}
    .resetpwd_wrap .form {
        display: block;
        width: 96%;
        margin: 0 auto;
        padding-top: 28px;
    }

    .resetpwd_wrap .form .item {
        display: -webkit-box;
        display: -ms-flexbox;
        display: flex;
        width: 100%;
        -webkit-box-align: center;
        -ms-flex-align: center;
        align-items: center;
        margin-bottom: 20px;
    }

    .resetpwd_wrap .form .item .inp {
        -webkit-box-flex: 1;
        -ms-flex: 1 1 auto;
        flex: 1 1 auto;
        border-radius: 5px;
        border: 1px solid #eeeeee;
        font-size: 14px;
        height: 46px;
        padding: 0 10px;
        background: #fff;
    }


    .get_captcha {
        flex: 0 0 40%;
        margin-left: 10px;
        font-size: 16px;
        text-align: center;
        line-height: 45px;
        color: #FFFFFF;
        height: 45px;
        background: linear-gradient(90deg, rgba(228, 57, 46, 1) 0%, rgba(255, 77, 22, 1) 100%);
        border-radius: 8px;
    }

    .resetpwd_wrap .form .basic_btn {
        display: block;
        margin: 35px auto 0 auto;
    }
</style>