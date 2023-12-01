import { createAsyncThunk, createSlice } from '@reduxjs/toolkit'
import { User } from '@/redux/types/response.type'
import { LoginInput, RegisterInput } from '@redux/types/request.type'
import { handleAxiosError, publicApi } from '@lib/axios'
import { setCookie } from '@lib/utils'
import AuthConfig from '@config/auth.config'

const initialState = {
  user: {} as User,
}

const userSlice = createSlice({
  name: 'user',
  initialState,
  reducers: {},
  extraReducers: builder => {
    builder.addCase(login.fulfilled, (state, action) => {
      state.user = action.payload
    })
  },
})
export const login = createAsyncThunk(
  'user/login',
  async (input: LoginInput, { rejectWithValue }) => {
    try {
      const formData = new FormData()
      for (const [key, value] of Object.entries(input)) {
        formData.append(key, value)
      }
      const { data } = await publicApi.post('/auth/login', formData)
      setCookie(AuthConfig.accessTokenKey, data.data.accessToken)
      setCookie(AuthConfig.refreshTokenKey, data.data.refreshToken)
      return data.data.user
    } catch (error) {
      const { message } = handleAxiosError(error)
      return rejectWithValue(message)
    }
  },
)

export const register = createAsyncThunk(
  'user/register',
  async (input: RegisterInput, { rejectWithValue }) => {
    try {
      const formData = new FormData()
      for (const [key, value] of Object.entries(input)) {
        formData.append(key, value)
      }
      const { data } = await publicApi.post('/auth/register', formData)
      setCookie(AuthConfig.accessTokenKey, data.data.accessToken)
      setCookie(AuthConfig.refreshTokenKey, data.data.refreshToken)
      return data.data.user
    } catch (error) {
      const { message } = handleAxiosError(error)
      return rejectWithValue(message)
    }
  },
)

export default userSlice
