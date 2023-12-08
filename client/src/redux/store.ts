import { configureStore } from '@reduxjs/toolkit'
import { TypedUseSelectorHook, useSelector } from 'react-redux'
import userSlice from './slices/user.slice'
import categorySlice from './slices/category.slice'

export const store = configureStore({
  reducer: {
    user: userSlice.reducer,
    category: categorySlice.reducer,
  },
})

export type RootState = ReturnType<typeof store.getState>
export type AppDispatch = typeof store.dispatch
export const useAppSelector: TypedUseSelectorHook<RootState> = useSelector
