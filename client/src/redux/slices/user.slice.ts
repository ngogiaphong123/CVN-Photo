import { createAsyncThunk, createSlice } from '@reduxjs/toolkit'
import { User } from '@/redux/types/response.type'
import {
  LoginInput,
  RegisterInput,
  UpdateProfileInput,
} from '@redux/types/request.type'
import { handleAxiosError, privateApi, publicApi } from '@lib/axios'
import { setCookie, unsetCookie } from '@lib/utils'
import AuthConfig from '@config/auth.config'

const initialState = {
  user: {} as User,
}

const userSlice = createSlice({
  name: 'user',
  initialState,
  reducers: {},
  extraReducers: builder => {
    builder
      .addCase(login.fulfilled, (state, action) => {
        state.user = action.payload
      })
      .addCase(register.fulfilled, (state, action) => {
        state.user = action.payload
      })
      .addCase(getMe.fulfilled, (state, action) => {
        state.user = action.payload
      })
      .addCase(logout.fulfilled, state => {
        state.user = {} as User
      })
      .addCase(updateProfile.fulfilled, (state, action) => {
        state.user = action.payload
      })
      .addCase(uploadAvatar.fulfilled, (state, action) => {
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

export const logout = createAsyncThunk(
  'user/logout',
  async (_, { rejectWithValue }) => {
    try {
      const { data } = await privateApi.get('/auth/logout')
      unsetCookie(AuthConfig.accessTokenKey)
      unsetCookie(AuthConfig.refreshTokenKey)
      return data.data
    } catch (error) {
      const { message } = handleAxiosError(error)
      return rejectWithValue(message)
    }
  },
)

export const getMe = createAsyncThunk(
  'user/getMe',
  async (_, { rejectWithValue }) => {
    try {
      const { data } = await privateApi.get('/auth/me')
      return data.data
    } catch (error) {
      const { message } = handleAxiosError(error)
      return rejectWithValue(message)
    }
  },
)

export const updateProfile = createAsyncThunk(
  'user/updateProfile',
  async (input: UpdateProfileInput, { rejectWithValue }) => {
    try {
      const formData = new FormData()
      for (const [key, value] of Object.entries(input)) {
        formData.append(key, value)
      }
      const { data } = await privateApi.post('/users/update-profile', formData)
      return data.data
    } catch (error) {
      const { message } = handleAxiosError(error)
      return rejectWithValue(message)
    }
  },
)

export const uploadAvatar = createAsyncThunk(
  'user/uploadAvatar',
  async (file: File | null, { rejectWithValue }) => {
    try {
      if (file !== null && file.size > 2 * 1024 * 1024) {
        return rejectWithValue('Avatar must be under 2MB')
      }
      if (
        file !== null &&
        !file.type.includes('jpeg') &&
        !file.type.includes('jpg') &&
        !file.type.includes('png')
      ) {
        return rejectWithValue('Avatar must be jpeg, jpg or png')
      }
      if (file === null) file = new File([], '')
      const formData = new FormData()
      formData.append('avatar', file)
      const { data } = await privateApi.post('/users/upload-avatar', formData)
      return data.data
    } catch (error) {
      const { message } = handleAxiosError(error)
      return rejectWithValue(message)
    }
  },
)

export default userSlice
