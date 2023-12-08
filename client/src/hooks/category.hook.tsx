import { useQuery } from '@tanstack/react-query'
import { privateApi } from '@lib/axios'
import { Category, Photo } from '@redux/types/response.type'

export const useCategoryPhotos = (categoryId: string | undefined) => {
  return useQuery({
    queryKey: ['categoryPhotos', categoryId],
    queryFn: async () => {
      const { data } = await privateApi.get(`/categories/${categoryId}/photos`)
      return data.data as Photo[]
    },
  })
}

export const useCategory = (categoryId: string | undefined) => {
  return useQuery({
    queryKey: ['category', categoryId],
    queryFn: async () => {
      const { data } = await privateApi.get(`/categories/${categoryId}`)
      return data.data as Category
    },
  })
}
