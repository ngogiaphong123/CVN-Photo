import { useInfiniteQuery } from '@tanstack/react-query'
import { privateApi } from '@/lib/axios'
import { Photo } from '@/redux/types/response.type'

export const useGetPhotosInCategoryByPage = (
  categoryId: string | undefined,
) => {
  const LIMIT = 20
  return useInfiniteQuery({
    queryKey: [`infinitePhotosInCategory${categoryId}`],
    queryFn: async ({ pageParam = 1 }) => {
      const { data } = await privateApi.get(
        `/categories/${categoryId}/photos/${pageParam}/${LIMIT}`,
      )
      return data.data as Photo[]
    },
    getNextPageParam: (_, pages) => {
      return pages.length + 1
    },
    initialPageParam: 1,
    refetchOnMount: 'always',
  })
}
