import { useQuery } from '@tanstack/react-query'
import { privateApi } from '@/lib/axios'
import { Photo } from '@/redux/types/response.type'

export const useGetPhotosNotInCategory = (categoryId: string) => {
  return useQuery({
    queryKey: [`photosNotInCategory${categoryId}`],
    queryFn: async () => {
      const { data } = await privateApi.get(
        `/categories/${categoryId}/photos/not-in-category`,
      )
      return data.data as Photo[]
    },
  })
}
