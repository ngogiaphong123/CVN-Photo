import { useQuery } from '@tanstack/react-query'
import { privateApi } from '@lib/axios'
import { Photo } from '@redux/types/response.type'

export const usePhoto = (photoId: string | undefined) => {
  return useQuery({
    queryKey: [`${photoId}`],
    queryFn: async () => {
      const { data } = await privateApi.get(`/photos/${photoId}`)
      return data.data as Photo
    },
    retry: false,
  })
}
