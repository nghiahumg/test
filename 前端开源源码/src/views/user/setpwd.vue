<template>
    <div class="basic_wrap">
        <div class="red_top_bg">
            <div class="back_left" @click="$router.back()"></div>
            <div class="big_tit">{{$t('setpwd.modifyLoginPassword')}}</div>
        </div>
        <div class="wrap">
            <div class="title">
                <p>{{$t('setpwd.setNewLoginPassword')}}</p>
                <div class="cut" v-if="!codeStatus" @click="cutType(1)">{{$t('setpwd.usemobilenumber')}}</div>
                <!-- <div class="cut" v-if="codeStatus" @click="cutType(2)">{{$t('setpwd.useoriginalpassword')}}</div> -->
            </div>
            <!--            <div class="title">
                <p>设置新的登录密码</p>
            </div> -->
            <p class="bd_tel" v-if="codeStatus">{{$t('setpwd.boundmobilenumber')}}{{ycmobile}}</p>
            <div class="inp_box" v-if="codeStatus">
                <div class="inp">
                    <input type="number" v-model="code" :placeholder="$t('setpwd.SMSverificationcode')">
                </div>
                <!-- <div class="getcode reset_code">重新获取验证码</div> -->
                <van-count-down :time="time" class="btn getcode" @finish="timeCall">
                    <template v-slot="timeData">
                        <span @click="sendcode">{{timeData.seconds > 0 ? timeData.seconds : $t('setpwd.sendcode')}}</span>
                    </template>
                </van-count-down>
            </div>

            <div class="write_box" v-if="!codeStatus">
                <input :type="passwordInp" v-model="password" :placeholder="$t('setpwd.originalloginpassword')">
                <div class="eye_bi" @click="showPwd" :class="passwordInp=='text'?'eye':''"></div>
            </div>
            <div class="write_new_pwd">
                <div class="write_box">
                    <input :type="newpasswordInp" v-model="npassword" :placeholder="$t('setpwd.newpassword')">
                    <div class="eye_bi" @click="showNewsPwd" :class="newpasswordInp=='text'?'eye':''"></div>
                </div>
                <div class="write_box">
                    <input :type="newpasswordInp" v-model="rpassword" :placeholder="$t('setpwd.confirmnewpassword')">
                    <div class="eye_bi" @click="showNewsPwd" :class="newpasswordInp=='text'?'eye':''"></div>
                </div>
            </div>
        </div>
        <div v-if="codeStatus" class="basic_btn" :class="rpassword!=''&&code!=''&&npassword!=''?'':'no_touch'" @click="dxRevise">{{$t('setpwd.modify')}}</div>
        <div v-if="!codeStatus" class="basic_btn" :class="password!=''&&rpassword!=''&&npassword!=''? '':'no_touch'"
            @click="oldRevise">{{$t('setpwd.modify')}}</div>
    </div>
</template>

<script>
    import Vue from 'vue';
    import {
        CountDown,
    } from 'vant';
    import Fetch from '../../utils/fetch'

    Vue.use(CountDown);

    export default {
        name: "setpwd",
        data() {
            return {
                time: 0,
                password: '',
                rpassword: '',
                npassword: '',
                mobile: '',
                ycmobile: '',
                passwordInp: 'password',
                newpasswordInp: 'password',
                codeStatus: true,
                codeOK: false,
                code: '',
                is_send: false
            };
        },
        created() {
            this.$parent.footer('user', false);
        },
        mounted() {
            Fetch('/user/info').then(r => {
                this.mobile = r.data.mobile;
                this.ycmobile = r.data.mobile.replace(/(\d{3})\d{4}(\d{4})/, '$1****$2');
            })
        },
        methods: {
            cutType(n) {
                n == 1 ? this.codeStatus = true : this.codeStatus = false;
                this.rpassword = '';
                this.npassword = '';
            },
            timeCall() {
                this.is_send = false;
                this.time = 0;
            },
            sendcode() {
                if (this.is_send) {
                    return;
                }
                this.is_send = true;
                Fetch("/index/forgetpwd_email_code", {
                }).then(() => {
                    this.time = 60 * 1000;
                    this.$toast({
                        background: '#07c160',
                        message: this.$t('setpwd.sendsuccessfully')
                    });
                }).catch(() => {
                    this.is_send = false;
                });

            },
            showPwd() {
                this.passwordInp == 'password' ? this.passwordInp = 'text' : this.passwordInp = 'password'
            },
            showNewsPwd() {
                this.newpasswordInp == 'password' ? this.newpasswordInp = 'text' : this.newpasswordInp = 'password'
            },
            dxRevise() {

                if (!this.npassword) {
                    this.$toast(this.$t('setpwd.newpasswordcannotbeempty'));
                    return false;
                }

                if (this.npassword !== this.rpassword) {
                    this.$toast(this.$t('setpwd.inconsistent'));
                    return false;
                }

                Fetch('/index/forgetpwd', {
                    code: this.code,
                    password: this.npassword,
                }).then(() => {

                    this.$toast({
                        background: '#07c160',
                        message: this.$t('setpwd.modifiedsuccessfully')
                    });

                    this.$router.replace('/safe')
                });

            },
            oldRevise() {
                if (!this.password) {
                    this.$toast(this.$t('setpwd.originalpasswordcannotbeempty'));
                    return false;
                }
                if (!this.npassword) {
                    this.$toast(this.$t('setpwd.newpasswordcannotbeempty'));
                    return false;
                }
                if (this.npassword !== this.rpassword) {
                    this.$toast(this.$t('setpwd.inconsistent'));
                    return false;
                }
                Fetch('/user/repasswd', {
                    passwd: this.password,
                    npasswd: this.npassword,
                }).then(() => {
                    this.$toast({
                        background: '#07c160',
                        message: this.$t('setpwd.modifiedsuccessfully')
                    });
                    this.$router.replace('/safe')
                });
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
    .no_touch {
        pointer-events: none;
    }

    .wrap {
        padding: 35px 20px 0 20px;
    }

    .title {
        width: 100%;
        height: 25px;
        line-height: 25px;
        display: flex;
        justify-content: space-between;
        align-items: center;

        p {
            font-size: 18px;
            color: #000;
            font-weight: 400;
        }

        .cut {
            font-size: 12px;
            color: #3D96FF;
        }
    }

    .bd_tel {
        margin-top: 23px;
        color: #000;
        font-size: 14px;
        font-weight: 400;
    }

    .inp_box {
        display: flex;
        justify-content: space-between;
        height: 45px;
        margin-top: 9px;

        .inp {
            width: 195px;
            border: 1px solid rgba(0, 0, 0, 0.1);
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 0 10px;
            border-radius: 2px;
            background-color: #FFFFFF;

            input {
                width: 100%;
                height: 22px;
                line-height: 22px;
                font-size: 16px;
            }
        }

        .getcode {
            flex: 1;
            margin-left: 10px;
            font-size: 16px;
            text-align: center;
            line-height: 45px;
            color: #FFFFFF;
            height: 45px;
            background: linear-gradient(90deg, rgba(228, 57, 46, 1) 0%, rgba(255, 77, 22, 1) 100%);
            border-radius: 8px;
        }

        .reset_code {
            background: rgba(228, 228, 228, 1);
            color: rgba(0, 0, 0, 0.3);
        }
    }

    .write_box {
        width: 100%;
        height: 45px;
        margin-top: 32px;
        background: rgba(255, 255, 255, 1);
        border-radius: 2px;
        border: 1px solid rgba(0, 0, 0, 0.1);
        display: flex;
        justify-content: center;
        align-items: center;
        padding: 0 10px;
        border-radius: 2px;
        background-color: #FFFFFF;

        input {
            width: 100%;
            height: 22px;
            line-height: 22px;
            font-size: 16px;
        }
    }

    .eye_bi {
        width: 21px;
        height: 25px;
        background: url(../images/user/eye2.png) no-repeat center center;
        background-size: 100% auto;

        &.eye {
            background: url(../images/user/eye1.png) no-repeat center center;
            background-size: 100% auto;
        }
    }

    .basic_btn {
        margin: 43px auto 0 auto;
    }

    .write_new_pwd {
        margin-top: 32px;

        .write_box:first-child {
            margin-bottom: 18px;
            margin-top: 0px;
        }

        .write_box:last-child {
            margin-top: 0px;
        }
    }
</style>
