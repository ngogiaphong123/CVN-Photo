import { useMutation, useQueryClient } from '@tanstack/react-query'
import { AppDispatch } from '@/redux/store'
import { useDispatch } from 'react-redux'
import { privateApi } from '@/lib/axios'
import { getCategories } from '@/redux/slices/category.slice'

export const useRemovePhotoFromCategory = (categoryId: string) => {
  const formData = new FormData()
  formData.append('categoryId', categoryId)
  const queryClient = useQueryClient()
  const dispatch = useDispatch<AppDispatch>()
  return useMutation({
    mutationKey: ['removePhotoFromCategory', categoryId],
    mutationFn: async (photoId: string) => {
      formData.append('photoId', photoId)
      const { data } = await privateApi.post(`photo-category/delete`, formData)
      return data.data
    },
    onSuccess: () => {
      queryClient.removeQueries({
        queryKey: ['infinitePhotos'],
      })
      queryClient.invalidateQueries({
        queryKey: [`categoryPhotos${categoryId}`],
      })
      queryClient.invalidateQueries({
        queryKey: [`${categoryId}`],
      })
      queryClient.removeQueries({
        queryKey: [`photosNotInCategory${categoryId}`],
      })
      dispatch(getCategories())
    },
  })
}
