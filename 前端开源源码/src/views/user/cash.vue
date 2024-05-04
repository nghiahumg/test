<template>
    <div class="withdraw_wrap">
		<div class="red_top_bg">
		    <div class="back_left" @click="$router.go(-1)"></div>
		    <div class="big_tit">{{$t('cash.withdraw')}}</div>
		</div>
		<div class="balance" v-if="eye==1">
			<p>
				{{$t('utils.moneyMark')}}{{data.money}}
			</p>
			<p>
				{{$t('invest.availableBalance')}}   
				<van-icon name="eye-o" @click="eye=0"/>
			</p>
		</div>
		<div class="balance" v-if="eye==0">
			<p>
				******
			</p>
			<p>
				{{$t('invest.availableBalance')}}    
				<van-icon name="closed-eye" @click="eye=1"/>
			</p>
		</div>
        <div class="wrap_box wrap1">
			<div class="choose_bank" @click="show=true">
				<div>
					<span>{{$t('cash.wallet')}}</span>
				</div>
				<div>
					<span>{{bank_name}}</span>
					<van-icon name="arrow" color="#999"/>
				</div>
			</div>
			<van-action-sheet
			  v-model="show"
			  :actions="actions"
			  cancel-text=""
			  :description="this.$t('cash.chooseWallet')"
			  close-on-click-action
			  @select="onSelect"
			/>
			<div class="withdraw_wrap">
				<p>{{$t('cash.amount')}}</p>
				<div class="flex_center">
					<span>{{$t('utils.moneyMark')}}</span>
					 <input
					  v-model="form.money" 
					  type="number"
					  step="0.01"
					  class="inp"
					  :placeholder="$t('cash.withdrawableAmount') + $t('utils.moneyMark')+data.money"
					  @input="changeAmount"
					>
					<span>{{actualAmount}}</span>
				</div>
				<p>{{chargeTip}}</p>
			</div>
            <p class="canal_tips">{{$t('cash.arrivalTime')}}</p>
        </div>
        <div class="basic_btn" @click="write_psd = true;submitType = 1;">{{$t('utils.submit')}}</div>
        <van-popup v-model="write_psd" closeable close-icon="close" round position="bottom">
            <div class="psw_box">
                <van-password-input :value="psd_val" :mask="true" :focused="showKeyboard" @focus="showKeyboard = true" />
                <div class="keybord_box">
                    <div class="keybord">
                        <div class="key_item" v-for="i in 9" @click="onInput(i)">
                            <div class="key_inner">{{i}}</div>
                        </div>
                        <div class="key_item no_bg">
                            <div class="key_inner"></div>
                        </div>
                        <div class="key_item" @click="onInput(0)">
                            <div class="key_inner">0</div>
                        </div>
                        <div class="key_item no_bg x" @click="onDelete">
                            <div class="key_inner">x</div>
                        </div>
                    </div>
                </div>
            </div>
        </van-popup>
		<div v-show="setPwd" class="real_name_mask">
				<div class="real_box">
					<div class="tit" />
					<p class="tips">
						{{$t('cash.setPwdFirst')}}
					</p>
					<div class="basic_btn" @click="setIniPwd">
						{{$t('cash.setNow')}}
					</div>
				</div>
			</div>
		</div>
    </div>
</template>

<script>
    import Vue from 'vue';
    import Fetch from '../../utils/fetch'
    import {
        DropdownMenu,
        DropdownItem,
		Popup,
		PasswordInput,
		NumberKeyboard,
		Icon,
		ActionSheet
    } from 'vant';
    Vue.use(PasswordInput).use(NumberKeyboard).use(Popup).use(DropdownMenu).use(DropdownItem).use(Icon).use(ActionSheet);

    export default {
        name: "cost",
        data() {
            return {
                data: {},
                showKeyboard: true,
                write_psd: false,
                psd_val: '',
				chargeTip:'',
				charge:0,
                form: {
                    money: '',
                    password: '',
                    bank_id: 0,
                },
				eye:1,
				show: false,
				bank_name:this.$t('cash.chooseWallet'),
				actions: [],
				setPwd:false,
				submitType:1,
				type:1,
				rate:1,
				mark:"$",
				actualAmount:''
            };
        },
        created() {
            this.$parent.footer('user', false);
        },
        mounted() {
            this.start();
        },
        methods: {
			onSelect(item){
				this.chargeTip = "";
				this.actualAmount = "";
				this.form.money = "";
				this.show = false;
				this.bank_name = item.name;
				this.form.bank_id = item.value;
				this.charge = item.charge;
				this.type = item.type;
				this.rate = item.rate;
				this.mark = item.mark;
				if(item.charge>0){
					this.chargeTip = this.$t('cash.charge')+item.charge+"%";
				}else{
					this.chargeTip = "";
				}
				if(item.value==0){
					this.$router.push('/addcountUsdt')
				}
			},
			changeAmount(){
				this.chargeTip = "";
				var rateTip = "";
				var num = 2;
				// if(this.rate!=1){
					if(this.type==1){
						if(this.rate>10000){
							num = 8;
						}else if(this.rate>1000){
							num = 6;
						}else if(this.rate>10){
							num = 4;
						}
						this.actualAmount = "≈"+(this.form.money/this.rate).toFixed(num)+this.mark;
						rateTip = "(≈"+(this.form.money*this.charge/100/this.rate).toFixed(num)+this.mark+")";
					}else{
						this.actualAmount = "≈"+this.mark+(this.form.money/this.rate).toFixed(num);
						rateTip = "(≈"+this.mark+(this.form.money*this.charge/this.rate/100).toFixed(num)+")";
					}
				// }
				if(this.charge>0){
					this.chargeTip = this.$t('cash.charge')+":$"+(this.form.money*this.charge/100).toFixed(num)+rateTip;
				}
			},
            onInput(key) {
                this.psd_val = (this.psd_val + key).slice(0, 6);
                if (this.psd_val.length == 6) {
					this.write_psd = false;
					this.form.password = this.psd_val;
					if(this.submitType==1){
						this.submit();
					}else{
						this.setIniPwdSubmit();
					}
					
                }
            },
            onDelete() {
                this.psd_val = this.psd_val.slice(0, this.psd_val.length - 1);
            },
            subAll() {
                this.form.money = this.data.money;
            },
            start() {
                Fetch('/user/my_bank').then((r) => {
                    this.data = r.data;
					this.setPwd = r.data.intPwd;
                    var bankvalue = [];
                    r.data.bank.forEach(item => {
						var length = item.account.length;
						var account = item.account+"";
						var itemAccount = account.substr(length-4,length)
                        bankvalue.push({
                            name: item.bank+"(****"+itemAccount+")",
                            value: item.id,
							charge:item.charge,
							rate:item.rate,
							type:item.type,
							mark:item.mark
                        })
                    })
					bankvalue.push({
					    name: this.$t('cash.addWallet'),
					    value: 0,
						color:"#FF0000",
					})
					this.actions = bankvalue;
					
                });
            },
            submit() {
				this.psd_val="";
                if (!this.form.money) {
                    this.$toast(this.$t('cash.withdrawalAmountEmpty'));
                    return false;
                }
                if(this.form.money < 0){
                    this.$toast(this.$t('cash.withdrawalAmountNotAllow'));
                    return false;
                }
				if(parseInt(this.form.money) > parseInt(this.data.money)){
                    this.$toast(this.$t('cash.withdrawalAmountHigh'));
                    return false;
                }
                if (this.form.bank_id == 0 || this.form.bank_id == -1) {
                    this.$toast(this.$t('cash.withdrawalAccountEmpty'));
                    return false;
                }
				if (!this.form.password) {
                    this.$toast(this.$t('cash.withdrawalPasswordEmpty'));
                    return false;
                }
                Fetch('/user/cost_apply', {
                    money: this.form.money,
                    bank_id: this.form.bank_id,
                    passwd: this.form.password
                }).then(() => {
                    this.$toast({
                        background: '#07c160',
                        message: this.$t('cash.success')
                    });
                    this.$router.replace('/user')
                });
            },
			setIniPwdSubmit(){
				this.psd_val="";
				Fetch('/user/setIniPwd', {
				    password: this.form.password
				}).then(() => {
					this.setPwd = false;
				});
			},
			setIniPwd(){
				this.submitType = 2;
				this.write_psd = true;
			}
        }
    };
</script>

<style lang="less" scoped>
	
	/deep/ .van-action-sheet__item {
	    text-align: left;
	}
	/deep/ .van-popup--bottom{
		border-radius:0;
		padding-bottom: 15px;
	}
	
	.red_top_bg {
	    height: 126px;
	    background: #3775f4;
	    position: fixed;
	}
	.balance{
		width:96%;
		margin-left: 2%;
		background:rgba(255,255,255,1);
		border-radius:6px;
		padding: 20px 0px;
		position: absolute;
		top:78px;
		text-align: center;
		p:nth-child(1){
			font-size: 26px;
			font-weight: bold;
			margin-bottom: 15px;
		}
		p:nth-child(2){
			color: #999;
			font-weight: bold;
		}
	}

    .wrap1 {
        margin-top: 176px;
		width: 96%;
		margin-left: 2%;
		box-shadow: none;
		background: none;
		.choose_bank{
			width: 100%;
			background: #FFFFFF;
			border-radius: 5px;
			padding:20px;
			display: flex;
			justify-content: flex-start;
			align-items: center;
			div:nth-child(1){
				width: 32%;
				font-size: 16px;
				font-weight: bold;
				color: #999;
			}
			div:nth-child(2){
				width: 68%;
				text-align: right;
				font-size: 16px;
				font-weight: bold;
				color: #999;
			}
		}
		.withdraw_wrap{
			width: 100%;
			background: #ffffff;
			border-radius: 6px;
			padding: 16px 20px;
			margin-top: 10px;
			
			input{
				height: 18px;
				width: 60%;
				line-height: 18px;
				font-size: 14px;
			}
			div{
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
			p:nth-child(1){
				font-size: 16px;
				font-weight: bold;
				color: #999;
				margin-bottom: 20px;
			}
			p:nth-child(3){
				color: #999;
				margin-top: 20px;
			}
		
		}
    }
	.canal_tips {
        padding: 10px 16px 14px 16px;
        line-height: 17px;
        font-size: 12px;
        color: #f12211;
        border-top: 1px solid #ECECEC;
    }
    .basic_btn {
        margin: 53px auto 0 auto;
		background: #3775f4;
    }

    /deep/ .van-password-input {
        margin: 50px 28px 0 28px;
    }

    /deep/ .van-hairline--left::after {
        border: 1px solid #ECECEC;
    }

    .van-password-input .van-password-input__security::after {
        border-radius: 0;
    }

    .keybord_box {
        height: 250px;
        background-color: #F0F0F0;
        margin-top: 35px;
        padding: 24px 12px 0 12px;

        .keybord {
            width: 361px;
            margin: 24px auto 0 atuo;
            display: flex;
            justify-content: space-between;
            flex-wrap: wrap;
            text-align: center;

            .key_item {
                width: 115px;
                height: 44px;
                line-height: 44px;
                font-size: 18px;
                font-weight: bold;
                margin-bottom: 6px;
                background: rgba(255, 255, 255, 1);
                border-radius: 3px;

                &.no_bg {
                    background: none;
                }

                &.x {
                    color: #757880;
                    font-weight: bold;
                }
            }
        }
    }
</style>
