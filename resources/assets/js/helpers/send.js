import axios from 'axios'
import Auth from '../store/auth'


export function post(url, data) {
	return axios({
		method: "post",
		url: url,
		data: data,
		headers: {
			'Authorization': 'Bearer ' + Auth.state.api_token
		}
	})
}
export function get(url, data = null) {
	return axios({
		method: "get", 
		url: url,
		params: data,
		headers: {
			'Authorization': 'Bearer ' + Auth.state.api_token
		}
	})
}
export function del(url, data) {
	return axios({
		method: "delete", 
		url: url,
		data: data,
		headers: {
			'Authorization': 'Bearer ' + Auth.state.api_token
		}
	})
}