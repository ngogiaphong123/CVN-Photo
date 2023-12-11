import { useMutation, useQuery, useQueryClient } from '@tanstack/react-query'
import { privateApi } from '@lib/axios'
import { Category, Photo } from '@redux/types/response.type'
import { AppDispatch } from '@redux/store'
import { useDispatch } from 'react-redux'
import { getCategories } from '@redux/slices/category.slice'

export const useCategoryPhotos = (categoryId: string | undefined) => {
  return useQuery({
    queryKey: [`categoryPhotos${categoryId}`],
    queryFn: async () => {
      const { data } = await privateApi.get(`/categories/${categoryId}/photos`)
      return data.data as Photo[]
    },
  })
}

export const useCategory = (categoryId: string | undefined) => {
  return useQuery({
    queryKey: [categoryId],
    queryFn: async () => {
      const { data } = await privateApi.get(`/categories/${categoryId}`)
      return data.data as Category
    },
  })
}

export const useAddPhotoToCategory = (categoryId: string, photoId: string) => {
  const formData = new FormData()
  formData.append('photoId', photoId)
  formData.append('categoryId', categoryId)
  const queryClient = useQueryClient()
  const dispatch = useDispatch<AppDispatch>()
  return useMutation({
    mutationKey: ['addPhotoToCategory', categoryId],
    mutationFn: async () => {
      const { data } = await privateApi.post(`/photo-category`, formData)
      return data.data
    },
    onSuccess: () => {
      queryClient.invalidateQueries({
        queryKey: ['infinitePhotos'],
      })
      queryClient.invalidateQueries({
        queryKey: [`categoryPhotos${categoryId}`],
      })
      queryClient.invalidateQueries({
        queryKey: [`${categoryId}`],
      })
      dispatch(getCategories())
    },
  })
}

export const useRemovePhotoFromCategory = (
  categoryId: string,
  photoId: string,
) => {
  const formData = new FormData()
  formData.append('photoId', photoId)
  formData.append('categoryId', categoryId)
  const queryClient = useQueryClient()
  const dispatch = useDispatch<AppDispatch>()
  return useMutation({
    mutationKey: ['removePhotoFromCategory', categoryId],
    mutationFn: async () => {
      const { data } = await privateApi.post(`photo-category/delete`, formData)
      return data.data
    },
    onSuccess: () => {
      queryClient.invalidateQueries({
        queryKey: ['infinitePhotos'],
      })
      queryClient.invalidateQueries({
        queryKey: [`categoryPhotos${categoryId}`],
      })
      queryClient.invalidateQueries({
        queryKey: [`${categoryId}`],
      })
      dispatch(getCategories())
    },
  })
}
