<template>
    <div class="recharge_wrap">
        <div class="red_top_bg">
            <div class="back_left" @click="$router.back()"></div>
            <div class="big_tit">{{$t('account.manage')}}</div>
        </div>
        <div class="wrap_box warp1">
            <p><span>{{allNum}}</span> {{$t('account.withdrawalAccounts')}}</p>
        </div>
        <div class="wrap_box warp2">
            <div class="item" v-for="i in data.bank">
                <img :src="require('../images/user/recharge'+i.type+'.png')" alt="">
                <div class="item_detail">
                    <p class="user_name ari_font">{{i.bank}}</p>
                    <p class="user_num">{{i.account}}</p>
                </div>
                <div class="del" @click="delCard(i.id)"></div>
            </div>
        </div>
        <div class="basic_btn" @click="$router.push('/addcountUsdt')">{{$t('account.add')}}</div>
    </div>
</template>

<script>
    import Fetch from '../../utils/fetch'


    export default {
        name: "account",
        data() {
            return {
                allNum:0,
                data: {}
            };
        },
        created() {
            this.$parent.footer('user', false);
        },
        mounted() {
            this.start();
        },
        methods: {
            delCard(id){
                Fetch('/user/bank_remove', {id: id}).then(() => {
                    this.start()
                });
            },
            start(){
                Fetch('/user/my_bank').then(r=>{
                    this.data = r.data;
                    this.allNum = r.data.count;
                })
            }
        }
    };
</script>

<style lang="less" scoped>
    .red_top_bg {
        position: relative;
    }
	.back_left {
		background: url(../../views/images/item/back_b.png) no-repeat center center;
	}
	.big_tit{
		color: #000000;
	}

    .warp1 {
        padding: 0 20px;
        height: 40px;

        p {
            font-size: 14px;
            line-height: 40px;
            color: rgba(0,0,0,.8);
            font-weight: bold;

            span {
                color: #F03041;
            }
        }
    }

    .ari_font {
        height: 20px;
        font-size: 14px;
        font-weight: 600;
        color: rgba(0,0,0,.8);
        line-height: 20px;
    }

    .warp2 {
        padding: 0 15px;
        margin-top: 12px;

        .item {
            padding: 14px 0 11px 0;
            display: flex;
            align-items: center;
            justify-content: flex-start;
            border-bottom: 1px solid #ECECEC;

            &:last-child {
                border-bottom: none;
            }

            img {
                width: 30px;
                height: 30px;
            }

            .item_name {
                width: 65px;
            }
			.item_detail{
				margin-left: 10px;
			}

            .user_num {
                height: 17px;
                font-size: 12px;
                font-weight: 600;
                color: rgba(0,0,0,.6);
                line-height: 17px;
                margin-top: 10px;
            }

            .del {
                width: 20px;
                height: 20px;
                background: url(../images/item/del.png) no-repeat center center;
                background-size: 100% 100%;
                margin: 0 8px 0 auto;
            }
        }
    }

    .basic_btn {
        margin: 50px auto 0 auto;
    }
</style>
