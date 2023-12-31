import { createAsyncThunk, createSlice } from '@reduxjs/toolkit'
import { Category } from '@redux/types/response.type'
import { handleAxiosError, privateApi } from '@lib/axios'
const initialState = {
  categories: [] as Category[],
  favoriteCategory: {} as Category,
}
const categorySlice = createSlice({
  name: 'category',
  initialState,
  reducers: {},
  extraReducers: builder => {
    builder.addCase(getCategories.fulfilled, (state, action) => {
      state.categories = action.payload
      for (const category of state.categories) {
        if (category.name === 'favorite') {
          state.favoriteCategory = category
        }
      }
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

export default categorySlice
