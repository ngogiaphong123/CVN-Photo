import { useDispatch } from 'react-redux'
import { AppDispatch } from '@/redux/store'
import { useMutation } from '@tanstack/react-query'
import { CreateCategoryInput } from '@/redux/types/request.type'
import { privateApi } from '@/lib/axios'
import { Category } from '@/redux/types/response.type'
import { getCategories } from '@/redux/slices/category.slice'
import { toastMessage } from '@/lib/utils'
import { useNavigate } from 'react-router-dom'

export const useCreateCategory = () => {
  const dispatch = useDispatch<AppDispatch>()
  const navigate = useNavigate()
  return useMutation({
    mutationKey: ['createCategory'],
    mutationFn: async (input: CreateCategoryInput) => {
      const formData = new FormData()
      for (const [key, value] of Object.entries(input)) {
        formData.append(key, value)
      }
      const { data } = await privateApi.post(`/categories`, formData)
      return data.data as Category
    },
    onSuccess: data => {
      console.log(data)
      dispatch(getCategories())
      navigate(`/category/${data.id}`)
    },
    onError: () => {
      toastMessage('Category name already exists!', 'destructive')
    },
  })
}
