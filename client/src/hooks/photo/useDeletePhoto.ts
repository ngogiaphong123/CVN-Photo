import { useMutation, useQueryClient } from '@tanstack/react-query'
import { privateApi } from '@lib/axios'
import { toastMessage } from '@lib/utils'

export const useDeletePhoto = (photoId: string | undefined) => {
  const queryClient = useQueryClient()
  return useMutation({
    mutationKey: ['deletePhoto'],
    mutationFn: async () => {
      const { data } = await privateApi.delete(`/photos/${photoId}`)
      return data.data as number
    },
    onSuccess: () => {
      queryClient.invalidateQueries({
        queryKey: ['infinitePhotos'],
      })
      toastMessage('Deleted photo!', 'default')
    },
  })
}
