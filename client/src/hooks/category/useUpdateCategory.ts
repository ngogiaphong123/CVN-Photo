import { useMutation, useQueryClient } from '@tanstack/react-query'
import { AppDispatch } from '@/redux/store'
import { useDispatch } from 'react-redux'
import { UpdateCategoryInput } from '@/redux/types/request.type'
import { privateApi } from '@/lib/axios'
import { Category } from '@/redux/types/response.type'
import { getCategories } from '@/redux/slices/category.slice'
import { toastMessage } from '@lib/utils'

export const useUpdateCategory = (categoryId: string) => {
  const queryClient = useQueryClient()
  const dispatch = useDispatch<AppDispatch>()
  return useMutation({
    mutationKey: ['updateCategory'],
    mutationFn: async (updateInput: UpdateCategoryInput) => {
      const formData = new FormData()
      for (const [key, value] of Object.entries(updateInput)) {
        formData.append(key, value)
      }
      const { data } = await privateApi.post(
        `/categories/${categoryId}`,
        formData,
      )
      return data.data as Category
    },
    onSuccess: () => {
      queryClient.invalidateQueries({
        queryKey: [`${categoryId}`],
      })
      dispatch(getCategories())
      toastMessage('Category updated successfully', 'default')
    },
  })
}
