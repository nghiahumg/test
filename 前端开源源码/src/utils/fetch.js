import axios from 'axios'
import qs from "qs"
import {
	Toast
} from 'vant'
import router from '@/router'
import api from "../interface/index"
//import {Notify} from 'vant'
import {
	Dialog
} from 'vant';

let loadingCount = 0;

export const showLoading = () => {
	if (loadingCount <= 0) {
		Toast.loading({
			// mask: true,
			duration: 20000
		})
	}
	loadingCount++
}

export const hideLoading = () => {
	loadingCount--;
	if (loadingCount <= 0) {
		Toast.clear()
		loadingCount = 0
	}
};

let ssid = localStorage.getItem('ssid');

export default async (url, data, opt, isLoad = true) => {
	let lang = localStorage.getItem("lang")||"en_us";

	if (isLoad) {
		showLoading();
	}

	url = api.commonUrl + "/api" + url;
	const set = {
		method: 'post',
		...opt
	};
	var token = localStorage.getItem('token');
	var language = localStorage.getItem("lang")||"en_us";
	return (set.method === 'post' ? axios.post(url, {
		...data,
		token,
		language
	}) : axios.get(url + '?' + qs.stringify({
		...data,
		token
	}))).then(r => {

		if (isLoad) {
			hideLoading();
		}

		if (r.status === 200 && r.data.code) {
			return r.data;
		}

		if (r.data.code === 0) {
			return r.data;
		}

		throw {
			msg: '网络错误，请稍后重试'
		};

	}).then(r => {

		if (r.code === 1) {
			return r
		} else {

			if (r.code === 0) {
				throw {
					msg: r.info
				}
			}

			if (!r.code) {
				throw {
					msg: '网络错误，请稍后重试！'
				}
			}

			if (r.code === 406) {
				router.push('/setpaypwd');
			}

			if (r.code === 405) {
				const current = {
					...router.currentRoute
				};
				router.push('/authEmail?redirect=' + current.path+"?id="+current.query.id+"&money="+current.query.money);
			}

			if (r.code === 410) {
				router.push('/setpaypwd');
			}

			if (r.code === 403 || r.code === 401) {

				if (r.code === 401) {
					localStorage.setItem('reset_time', 0);
				}

				const current = {
					...router.currentRoute
				};
				if (current && current.path !== '/' && !(['/', '/reg', '/forgetpwd'].includes(current
					.path))) {
					if (current.path !== '/login') {
						router.replace('/login?redirect=' + current.path)
					}
				}
			}
		}

		throw r;

	}).catch(r => {

		if (isLoad) {
			hideLoading();
		}

		var message = '网络错误，请稍后或更换网络重试！';

		if (r.info) {
			message = r.info[lang];
		}

		if (r.msg) {
			message = r.msg[lang];
		}
		// if (isLoad) {
			//增加一个判断，有返回值才弹窗，防止页面未加载完按返回时提示网络错误
		if (isLoad&&(r.info||r.msg)) {
			Dialog.alert({
				message: message,
				confirmButtonText: window.vm.$i18n.t('utils.confirm')
			});
		}
		//Notify(message);

		throw '';
	})
}
