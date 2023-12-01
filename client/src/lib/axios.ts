import axios from 'axios'
import { getCookie, setCookie } from '@lib/utils'
import AuthConfig from '@/config/auth.config'

const publicApi = axios.create({
  baseURL: import.meta.env.VITE_API_URL,
})

const privateApi = axios.create({
  baseURL: import.meta.env.VITE_API_URL,
  headers: {
    'Content-Type': 'application/json',
  },
})

privateApi.interceptors.request.use(
  config => {
    const accessToken = getCookie(AuthConfig.accessTokenKey)
    if (accessToken) {
      config.headers.Authorization = `Bearer ${accessToken}`
    }
    return config
  },
  error => {
    return Promise.reject(error)
  },
)

privateApi.interceptors.response.use(
  response => {
    return response
  },
  async error => {
    const originalRequest = error.config
    if (
      error.response.status === 401 &&
      error.response.data.message === AuthConfig.accessTokenExpired &&
      !originalRequest._retry
    ) {
      originalRequest._retry = true
      const refreshToken = getCookie(AuthConfig.refreshTokenKey)
      const { data } = await publicApi.post('/auth/refresh', {
        refreshToken,
      })
      if (data.statusCode === 201) {
        setCookie(AuthConfig.accessTokenKey, data.data.accessToken)
        setCookie(AuthConfig.refreshTokenKey, data.data.refreshToken)
        return privateApi(originalRequest)
      }
    }
    return Promise.reject(error)
  },
)
export function handleAxiosError(error: any) {
  if (error.response) {
    return { message: error.response.data.message }
  } else if (error.request) {
    return { message: 'No response was received' }
  } else {
    return { message: 'Internal server error' }
  }
}

export { publicApi, privateApi }
