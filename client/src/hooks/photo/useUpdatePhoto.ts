import { useMutation, useQueryClient } from '@tanstack/react-query'
import { UpdatePhotoInput } from '@/redux/types/request.type'
import { privateApi } from '@/lib/axios'
import { Photo } from '@/redux/types/response.type'

export const useUpdatePhoto = (photoId: string) => {
  const queryClient = useQueryClient()
  return useMutation({
    mutationKey: ['updatePhoto'],
    mutationFn: async (updateInput: UpdatePhotoInput) => {
      const formData = new FormData()
      for (const [key, value] of Object.entries(updateInput)) {
        formData.append(key, value)
      }
      const { data } = await privateApi.post(`/photos/${photoId}`, formData)
      return data.data as Photo
    },
    onSuccess: () => {
      queryClient.invalidateQueries({
        queryKey: [`${photoId}`],
      })
    },
  })
}
