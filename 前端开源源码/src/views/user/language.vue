<template>
	<div class="basic_wrap">
		<div class="red_top_bg">
			<div class="back_left" @click="$router.go(-1)"></div>
			<div class="big_tit">{{$t('safe.language')}}</div>
		</div>
		<div class="wrap_box">
			<div class="item" @click="changeLang('zh_cn',$event)">
				<div class="left flex_center">
					<div><img src="../images/language/zh_cn.png"></div>
					<div class="info">简体中文</div>
				</div>
				<div v-if="lang=='zh_cn'">✓</div>
			</div>
		</div>
		<div class="wrap_box">
			<div class="item" @click="changeLang('zh_hk',$event)">
				<div class="left flex_center">
					<div><img src="../images/language/zh_hk.png"></div>
					<div class="info">繁體中文</div>
				</div>
				<div v-if="lang=='zh_hk'">✓</div>
			</div>
		</div>
		<div class="wrap_box">
			<div class="item" @click="changeLang('en_us',$event)">
				<div class="left flex_center">
					<div><img src="../images/language/en_us.png"></div>
					<div class="info">English</div>
				</div>
				<div v-if="lang=='en_us'">✓</div>
			</div>
		</div>
		<div class="wrap_box">
			<div class="item" @click="changeLang('th_th',$event)">
				<div class="left flex_center">
					<div><img src="../images/language/th_th.png"></div>
					<div class="info">ภาษาไทย</div>
				</div>
				<div v-if="lang=='th_th'">✓</div>
			</div>
		</div>
		<div class="wrap_box">
			<div class="item" @click="changeLang('vi_vn',$event)">
				<div class="left flex_center">
					<div><img src="../images/language/vi_vn.png"></div>
					<div class="info">Người việt nam</div>
				</div>
				<div v-if="lang=='vi_vn'">✓</div>
			</div>
		</div>
		<div class="wrap_box">
			<div class="item" @click="changeLang('ja_jp',$event)">
				<div class="left flex_center">
					<div><img src="../images/language/ja_jp.png"></div>
					<div class="info">日本語</div>
				</div>
				<div v-if="lang=='ja_jp'">✓</div>
			</div>
		</div>
		<div class="wrap_box">
			<div class="item" @click="changeLang('ko_kr',$event)">
				<div class="left flex_center">
					<div><img src="../images/language/ko_kr.png"></div>
					<div class="info">한국어</div>
				</div>
				<div v-if="lang=='ko_kr'">✓</div>
			</div>
		</div>
		<div class="wrap_box">
			<div class="item" @click="changeLang('ms_my',$event)">
				<div class="left flex_center">
					<div><img src="../images/language/ms_my.png"></div>
					<div class="info">Melayu</div>
				</div>
				<div v-if="lang=='ms_my'">✓</div>
			</div>
		</div>
		<div class="wrap_box">
			<div class="item" @click="changeLang('tr_tr',$event)">
				<div class="left flex_center">
					<div><img src="../images/language/tr_tr.png"></div>
					<div class="info">Türkçe</div>
				</div>
				<div v-if="lang=='tr_tr'">✓</div>
			</div>
		</div>
		<div class="wrap_box">
			<div class="item" @click="changeLang('es_es',$event)">
				<div class="left flex_center">
					<div><img src="../images/language/es_es.png"></div>
					<div class="info">Español</div>
				</div>
				<div v-if="lang=='es_es'">✓</div>
			</div>
		</div>
	</div>
</template>

<script>
	import Fetch from '../../utils/fetch'
	import bsHeader from '../../components/bsHeader.vue'
	import {
		Toast
	} from 'vant'
	export default {
		name: "language",
		components: {
			bsHeader
		},
		data() {
			return {
				lang: this.$i18n.locale || "zh_cn",
			};
		},
		created() {},
		mounted() {},
		methods: {
			changeLang(lang, event) {
				Toast.loading({
					// mask: true,
					duration: 200
				})
				this.lang = lang;
				this.$i18n.locale = lang;
				localStorage.setItem("lang", lang);
				this.$router.go(-1);
			},
		}
	};
</script>

<style lang="less" scoped>
	.back_left {
		background: url(../../views/images/item/back_b.png) no-repeat center center;
	}

	.big_tit {
		color: #000000;
	}

	.wrap_box {
		display: flex;
		flex-direction: column;
		align-items: center;

		.item {
			height: 50px;
			width: 95%;
			padding: 16px 0;
			border-bottom: 1px solid #ECECEC;
			font-size: 14px;
			color: rgba(0, 0, 0, .8);
			font-weight: bold;
			line-height: 20px;
			display: flex;
			justify-content: space-between;

			.right {
				width: 20px;
				height: 20px;
				background: url(../images/user/arrow.png) no-repeat center center;
				background-size: 100% 100%;
			}

			.right_info {
				color: rgba(0, 0, 0, .6);
			}

			.tips {
				color: rgba(0, 0, 0, .6);
				margin-left: auto;
				margin-right: 4px;
				font-weight: normal;
			}

			.left {
				img {
					width: 33px;
					margin-top: 5px;
				}
			}
		}
	}
</style>
