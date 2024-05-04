<template>
    <div class="newdetail_wrap">
		<div class="red_top_bg">
			<div class="back_left" @click="$router.back()"></div>
			<div class="big_tit">{{data.title}}</div>
		</div>
        <div class="ctn">
            <!-- <vue-editor v-model="data.content" style="display: none"/> -->
            <div class="contract_box" v-html="data.content"></div>
        </div>
    </div>
</template>

<script>

    import Fetch from "../../utils/fetch";
	import bsHeader from '../../components/bsHeader.vue'

    export default {
        name: "aritcle",
        components: {bsHeader},
        data() {
            return {
                data: {},
            };
        },
        created() {
            this.$parent.footer('user',false);
        },
        mounted() {
            this.start();
        },
        methods: {
            start() {
                Fetch('/index/article_detail', {
                    id: this.$router.history.current.params.code
                }).then(res => {
                    this.data = res.data;
                })
            },
        }
    };
</script>

<style lang="less" scoped>
    .red_top_bg{
        position: fixed;
        top: 0;
        left: 50%;
        transform: translateX(-50%);
        z-index: 10;
    }
	.back_left {
		background: url(../../views/images/item/back_b.png) no-repeat center center;
	}
	.big_tit{
		color: #000000;
	}
    .newdetail_wrap {
        overflow-x: hidden;
        overflow-y: auto;
        background: #f5f5f5;
    }

    .newdetail_wrap .ctn {
        width: 100%;
        background: #fff;
        padding: 5px;
        margin: 44px auto 0 auto;
    }
	.contract_box{
		line-height: 20px;
	}

    .newdetail_wrap .ctn .title {
        font-size: 12px;
        color: #333;
        font-weight: bold;
        line-height: 1.7;
    }

    .newdetail_wrap .ctn .info {
        font-size: 12px;
        color: #333;
        line-height: 1.7;
    }
</style>