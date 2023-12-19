import { useQuery } from '@tanstack/react-query'
import { privateApi } from '@/lib/axios'
import { Category } from '@/redux/types/response.type'

export const useCategory = (categoryId: string | undefined) => {
  return useQuery({
    queryKey: [categoryId],
    queryFn: async () => {
      const { data } = await privateApi.get(`/categories/${categoryId}`)
      return data.data as Category
    },
  })
}
