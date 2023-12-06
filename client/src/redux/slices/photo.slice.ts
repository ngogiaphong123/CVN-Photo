import { Photo } from '@redux/types/response.type'
import { createAsyncThunk, createSlice } from '@reduxjs/toolkit'
import { handleAxiosError, privateApi } from '@lib/axios'
const initialState = {
  photos: [] as Photo[],
}
const photoSlice = createSlice({
  name: 'photo',
  initialState,
  reducers: {},
  extraReducers: builder => {
    builder.addCase(getPhotos.fulfilled, (state, action) => {
      state.photos = action.payload
    })
    builder.addCase(uploadPhotos.fulfilled, (state, action) => {
      state.photos = action.payload
    })
  },
})

export const getPhotos = createAsyncThunk(
  'photo/getPhotos',
  async (_, { rejectWithValue }) => {
    try {
      const { data } = await privateApi.get('/photos')
      return data.data
    } catch (error) {
      const { message } = handleAxiosError(error)
      return rejectWithValue(message)
    }
  },
)

export const uploadPhotos = createAsyncThunk(
  'photo/uploadPhotos',
  async (files: FileList, { rejectWithValue }) => {
    try {
      const formData = new FormData()
      for (let i = 0; i < files.length; i++) {
        formData.append('photos[]', files[i])
      }
      const { data } = await privateApi.post('/photos', formData)
      return data.data
    } catch (error) {
      const { message } = handleAxiosError(error)
      return rejectWithValue(message)
    }
  },
)

export default photoSlice
