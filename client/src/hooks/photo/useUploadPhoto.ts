import { useMutation, useQueryClient } from '@tanstack/react-query'
import { privateApi } from '@/lib/axios'
import { Photo } from '@/redux/types/response.type'

export const useUploadPhoto = () => {
  const queryClient = useQueryClient()
  return useMutation({
    mutationKey: ['uploadPhoto'],
    mutationFn: async (files: FileList) => {
      for (let i = 0; i < files.length; i++) {
        if (files[i].size > 1024 * 1024 * 2) {
          throw new Error('Max file size is 2MB')
        }
        if (files[i].type !== 'image/jpeg' && files[i].type !== 'image/png') {
          throw new Error('Only support jpeg and png')
        }
      }
      if (!files.length) throw new Error('No files selected')
      const formData = new FormData()
      for (let i = 0; i < files.length; i++) {
        formData.append('photos[]', files[i])
      }
      const { data } = await privateApi.post('/photos', formData)
      return data.data as Photo
    },
    onSuccess: () => {
      queryClient.invalidateQueries({
        queryKey: ['infinitePhotos'],
      })
    },
  })
}
