import { useInfiniteQuery } from '@tanstack/react-query'
import { privateApi } from '@lib/axios'
import { Photo } from '@redux/types/response.type'

export const useGetPhotosByPage = () => {
  const LIMIT = 20

  return useInfiniteQuery({
    queryKey: ['infinitePhotos'],
    queryFn: async ({ pageParam = 1 }) => {
      const { data } = await privateApi.get(`/photos/${pageParam}/${LIMIT}`)
      if (data.data.length === 0) throw new Error('No more photos')
      return data.data as Photo[]
    },
    enabled: true,
    getNextPageParam: (_, pages) => {
      return pages.length + 1
    },
    initialPageParam: 1,
    refetchOnMount: 'always',
    retry: false,
  })
}
