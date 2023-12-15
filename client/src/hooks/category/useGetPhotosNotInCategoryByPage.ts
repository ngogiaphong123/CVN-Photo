import { useInfiniteQuery } from '@tanstack/react-query'
import { privateApi } from '@/lib/axios'
import { Photo } from '@/redux/types/response.type'

export const useGetPhotosNotInCategoryByPage = (categoryId: string) => {
  const LIMIT = 20
  return useInfiniteQuery({
    queryKey: [`photosNotInCategory${categoryId}`],
    queryFn: async ({ pageParam = 1 }) => {
      const { data } = await privateApi.get(
        `/categories/${categoryId}/photos/not-in-category/${pageParam}/${LIMIT}`,
      )
      if (data.data.length === 0) throw new Error('No more photos')
      return data.data as Photo[]
    },
    getNextPageParam: (_, pages) => {
      return pages.length + 1
    },
    initialPageParam: 1,
    refetchOnMount: 'always',
    retry: false,
    refetchOnWindowFocus: false,
  })
}
