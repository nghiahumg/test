<template>
    <div class="recharge_wrap">
        <div class="red_top_bg">
            <div class="back_left" @click="$router.go(-1)"></div>
            <div class="big_tit">{{$t('account.addAccount')}}</div>
        </div>
        <div class="wrap_box warp1">
            <div class="item">
                <div class="ari_font">{{$t('account.bank')}}：</div>
				<van-dropdown-menu>
				<van-dropdown-item v-model="value" :options="wallets" @change="onChange()"/>
				</van-dropdown-menu>
            </div>
			<!-- 虚拟货币 -->
			<div v-show="type == 1">
				<div class="item">
					<div class="ari_font">{{$t('account.address')}}：</div>
					<input class="card" type="text" v-model="bank.account" :placeholder="$t('account.addressTit')">
				</div>
				<div class="qrcode">
					<div>{{$t('account.qrcodetips')}}</div>
					<div class="qrcode_img">
						<!-- <span>QR code</span> -->
						<van-uploader v-model="fileList" multiple :max-count="1" class="upload" :max-size="2 * 1024 * 1024" @oversize="onOversize" :after-read="afterRead"
							:before-read="beforeRead">
						</van-uploader>
					</div>
				</div>
			</div>
			<!-- 支付宝 -->
			<div v-show="type == 2">
				<div class="item">
					<div class="ari_font">{{$t('accountUsdt.alipayAccount')}}</div>
					<input class="card" type="text" v-model="bank.account" :placeholder="$t('accountUsdt.alipayAccountEmpty')">
				</div>
				<div class="qrcode">
					<div>{{$t('accountUsdt.alipayQrcode')}}</div>
					<div class="qrcode_img">
						<van-uploader v-model="fileList" multiple :max-count="1" class="upload" :max-size="2 * 1024 * 1024" @oversize="onOversize" :after-read="afterRead"
							:before-read="beforeRead">
						</van-uploader>
					</div>
				</div>
			</div>
			<!-- 微信 -->
			<div v-show="type == 3">
				<div class="item">
					<div class="ari_font">{{$t('accountUsdt.wechatAccount')}}</div>
					<input class="card" type="text" v-model="bank.account" :placeholder="$t('accountUsdt.wechatAccountEmpty')">
				</div>
				<div class="qrcode">
					<div>{{$t('accountUsdt.wechatQrcode')}}</div>
					<div class="qrcode_img">
						<van-uploader v-model="fileList" multiple :max-count="1" class="upload" :max-size="2 * 1024 * 1024" @oversize="onOversize" :after-read="afterRead"
							:before-read="beforeRead">
						</van-uploader>
					</div>
				</div>
			</div>
			<!-- 银行卡 -->
			<div v-show="type == 4">
				<div class="item">
					<div class="ari_font">{{$t('accountUsdt.name')}}</div>
					<input class="card" type="text" v-model="bank.name" :placeholder="$t('accountUsdt.nameEmpty')">
				</div>
				<div class="item">
					<div class="ari_font">{{$t('accountUsdt.account')}}</div>
					<input class="card" type="text" v-model="bank.account" :placeholder="$t('accountUsdt.accountEmpty')">
				</div>
				<div class="item">
					<div class="ari_font">{{$t('accountUsdt.area')}}</div>
					<input class="card" type="text" v-model="bank.area" :placeholder="$t('accountUsdt.areaEmpty')">
				</div>
			</div>
        </div>
        <div class="basic_btn" @click="submitBtn">{{$t('account.submit')}}</div>
    </div>
</template>

<script>
    import Vue from 'vue';
    import Fetch from '../../utils/fetch'
	import Api from "../../interface/index";
	import {
        Overlay,Uploader,Button,DropdownMenu, DropdownItem,Toast
    } from 'vant';
    import axios from 'axios'

    Vue.use(Overlay).use(Uploader).use(Button).use(DropdownMenu).use(DropdownItem);
    export default {
        name: "addcountUsdt",
        data() {
            return {
                bank: {
                    account: '',
					name:'',
                    area: '',
                    bank:'',
					img:'',
					type:1,
					wid:0
                },
				fileList: [],
				type:1,
				value:0,
				wallets:[]
            };
        },
        created() {
            this.$parent.footer('user', false);
        },
        mounted() {
            this.start();
        },
        methods: {
			start(){
				Fetch('/user/wallet_type',{
				}).then(r=>{
					this.bank.bank = r.data[0].name;
					this.bank.type = r.data[0].type;
					this.type = r.data[0].type;
					this.bank.wid = r.data[0].id;
					for (let i = 0; i < r.data.length; i++) {
						this.wallets.push({
						    text: r.data[i].name,
						    value: i,
							type:r.data[i].type,
							wid:r.data[i].id
						})
					}
				})
			},
            submitBtn(){
				// if (!this.bank.card) {
				//     this.$notify(this.$t('account.addressEmpty'));
				//     return;
				// }
				// if (!this.bank.img) {
				//     this.$notify(this.$t('account.qrcodeEmpty'));
				//     return;
				// }
                Fetch('/user/bank_add',{
                    ...this.bank
                }).then(r=>{
					this.$router.go(-1);
                })
            },
			onOversize(file) {
			    this.$toast(this.$t('account.within2megabytes'));
			},
			beforeRead(file) {
			    if (file.type !== 'image/jpeg' && file.type !== 'image/jpg' && file.type !== 'image/png') {
			        this.$toast(this.$t('account.supportPicture'));
			        return false;
			    }
			    return true;
			},
			afterRead(file) {
			    let formData = new FormData();
				formData.append('language', this.$i18n.locale||"zh_cn");
			    formData.append('file', file.file);
			    formData.append('id', this.$router.history.current.params.id);
				formData.append('token', localStorage.getItem('token'));
				Toast.loading({
					forbidClick: true,
					duration: 20000
				});
			    axios.post(Api.commonUrl+"/api/index/bank_link_upload", formData).then((r) => {
					Toast.clear();
					if (r.data.code === 1) {
						this.bank.img = r.data.data;
			        } else {
			            if (r.data.info) {
			                this.$toast(r.data.info);
			            } else {
			                this.$toast(this.$t('account.uploadFail'));
			            }
			        }
			    });
			},
			onChange(){
				this.type = this.wallets[this.value]['type'];
				this.bank.bank = this.wallets[this.value]['text'];
				this.bank.type = this.wallets[this.value]['type'];
				this.bank.wid = this.wallets[this.value]['wid'];
				console.log(this.wallets)
			}
            
        }
    };
</script>

<style lang="less" scoped>
	// /deep/ .van-overlay{
	// 	background-color:hsla(0,0%,100%,0);
	// }
 //    /deep/ .van-dropdown-menu {
 //        position: relative;
	// 	width: 100%;
 //        z-index: 10;
 //        color: #CCCCCC;
 //    }

    /deep/ .van-dropdown-menu__bar {
		box-shadow:none;
        // position: relative;
        // z-index: 10;
        // height: 34px;
        // border: 1px solid #D9D9D9;
        // border-radius: 4px;
        // text-align: left;
    }
 //    /deep/ .van-dropdown-item--down {
 //        margin: 0 auto;
 //        z-index: 12;
 //        transform: translateX(4px);
 //    }

    // /deep/ .van-dropdown-menu__item {
    //     justify-content: flex-start;
    //     color: #ccc;
    //     background-color: #fff;
    // }

 //    /deep/ .van-dropdown-menu__title {
 //        width: 100%;
 //    }

 //    /deep/ .van-dropdown-menu__title::after {
 //        width: 13px;
 //        height: 22px;
 //        transform: none;
 //        background: url(../../assets/images/drop.png) no-repeat center center;
 //        background-size: 100% 100%;
 //        border: none;
 //        opacity: 1;
 //        margin-top: -11px;
 //        right: 10px;
 //    }

 //    /deep/ .van-cell {
 //        padding: 5px 8px;
 //    }

 //    /deep/ .van-cell__title {
 //        flex: 3;
 //        white-space: normal;
 //    }

 //    /deep/ .van-ellipsis {
 //        color: #000;
 //    }

 //    /deep/ .van-dropdown-item__option {
 //        color: #ccc;
 //        border: 1px solid #D9D9D9;
 //        border-bottom: none;
 //        border-radius: 4px;
 //    }

 //    /deep/ .van-dropdown-item__option:last-child {
 //        border-bottom: 1px solid #D9D9D9;
 //    }

 //    /deep/ .van-dropdown-item__option--active.van-dropdown-item__option {
 //        color: #000;

 //        .van-dropdown-item__icon {
 //            color: #E96259;
 //        }
 //    }
	/deep/ .van-button--primary {
	    background-color: transparent;
	    border: none;
	    width: 100%;
		height: 100%;
	}
	/deep/ .van-uploader__input-wrapper {
	    width: 100%;
	}
	/deep/ .van-uploader__wrapper{
		width: 100%;
		height: 100%;
	}
	/deep/ .van-uploader__upload{
		width: 100%;
		height: 100%;
		margin:0;
	}
	/deep/ .van-uploader__preview{
		margin:0;
	}
	/deep/ .van-uploader__preview-image{
		width: 100%;
		height: 100%;
	}
	
	.back_left {
		background: url(../../views/images/item/back_b.png) no-repeat center center;
	}
	.big_tit{
		color: #000000;
	}

    .rela {
        position: relative;
        z-index: 11;
    }

    .warp1 {
        margin-top: 12px;

        .item {
            height: 50px;
            padding: 0 16px;
            font-size: 14px;
            display: flex;
            justify-content: flex-start;
            align-items: center;
            border-bottom: 1px solid #ECECEC;

            &:last-child {
                border-bottom: none;
            }
			.card{
				width: 100%;
			}
            input {
                line-height: 14px;
            }
        }
    }

    .ari_font {
        word-break: keep-all;
        white-space: nowrap;
        height: 20px;
        font-size: 14px;
        font-weight: 600;
        color: rgba(0,0,0,.8);
        line-height: 20px;
		margin-right: 5px;
    }

    .basic_btn {
        margin: 75px auto 0 auto;
    }
	.qrcode{
		padding-top: 30px;
		padding-bottom: 30px;
		text-align: center;
		div{
			color: #999;
			font-size: 14px;
			margin-bottom: 20px;
		}
		.upload{
			width: 100%;
			height: 100%;
		}
		.qrcode_img{
			width: 250px;
			height: 250px;
			margin: 0 auto;
			background: linear-gradient(to left, #999, #999) left top no-repeat, 
						linear-gradient(to bottom, #999, #999) left top no-repeat, 
						linear-gradient(to left, #999, #999) right top no-repeat,
						linear-gradient(to bottom, #999, #999) right top no-repeat, 
						linear-gradient(to left, #999, #999) left bottom no-repeat,
						linear-gradient(to bottom, #999, #999) left bottom no-repeat,
						linear-gradient(to left, #999, #999) right bottom no-repeat,
						linear-gradient(to left, #999, #999) right bottom no-repeat;
			background-size: 1px 20px, 20px 1px, 1px 20px, 20px 1px; 
			span{
				line-height: 220px;
			}
			img{
				width: 240px;
				height: 240px;
				position: relative;
				top: -255px;
			}
		}
	}
</style>
