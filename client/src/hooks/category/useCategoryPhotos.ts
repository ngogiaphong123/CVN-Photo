import { useQuery } from '@tanstack/react-query'
import { privateApi } from '@/lib/axios'
import { Photo } from '@/redux/types/response.type'

export const useCategoryPhotos = (categoryId: string | undefined) => {
  return useQuery({
    queryKey: [`categoryPhotos${categoryId}`],
    queryFn: async () => {
      const { data } = await privateApi.get(`/categories/${categoryId}/photos`)
      return data.data as Photo[]
    },
  })
}
