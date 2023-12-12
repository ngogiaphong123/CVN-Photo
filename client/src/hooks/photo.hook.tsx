import {
  useInfiniteQuery,
  useMutation,
  useQuery,
  useQueryClient,
} from '@tanstack/react-query'
import { privateApi } from '@/lib/axios'
import { Photo } from '@/redux/types/response.type'
import { UpdatePhotoInput } from '@redux/types/request.type'
import { useToast } from '@components/ui/use-toast'

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

export const useDeletePhoto = (photoId: string | undefined) => {
  const queryClient = useQueryClient()
  const { toast } = useToast()
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
      toast({
        description: `Deleted photo!`,
      })
    },
  })
}

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
