import { useDispatch } from 'react-redux'
import { AppDispatch } from '@/redux/store'
import { useMutation } from '@tanstack/react-query'
import { privateApi } from '@/lib/axios'
import { getCategories } from '@/redux/slices/category.slice'

export const useDeleteCategory = () => {
  const dispatch = useDispatch<AppDispatch>()
  return useMutation({
    mutationKey: ['deleteCategory'],
    mutationFn: async (categoryId: string) => {
      const { data } = await privateApi.delete(`/categories/${categoryId}`)
      return data.data as number
    },
    onSuccess: () => {
      dispatch(getCategories())
    },
  })
}
