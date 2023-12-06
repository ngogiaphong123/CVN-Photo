import { createAsyncThunk, createSlice } from '@reduxjs/toolkit'
import { Category } from '@redux/types/response.type'
import { handleAxiosError, privateApi } from '@lib/axios'
const initialState = {
  categories: [] as Category[],
  category: {} as Category,
}
const categorySlice = createSlice({
  name: 'category',
  initialState,
  reducers: {},
  extraReducers: builder => {
    builder
      .addCase(getCategories.fulfilled, (state, action) => {
        state.categories = action.payload
      })
      .addCase(getCategory.fulfilled, (state, action) => {
        state.category = action.payload
      })
  },
})
export const getCategories = createAsyncThunk(
  'category/getCategories',
  async (_, { rejectWithValue }) => {
    try {
      const { data } = await privateApi.get('/categories')
      return data.data
    } catch (error) {
      const { message } = handleAxiosError(error)
      return rejectWithValue(message)
    }
  },
)

export const getCategory = createAsyncThunk(
  'category/getCategory',
  async (categoryId: string | undefined, { rejectWithValue }) => {
    try {
      if (!categoryId) return rejectWithValue('Category id is undefined')
      const { data } = await privateApi.get(`/categories/${categoryId}`)
      return data.data
    } catch (error) {
      const { message } = handleAxiosError(error)
      return rejectWithValue(message)
    }
  },
)

export default categorySlice
