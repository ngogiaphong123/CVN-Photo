import {
  useInfiniteQuery,
  useMutation,
  useQuery,
  useQueryClient,
} from '@tanstack/react-query'
import { privateApi } from '@lib/axios'
import { Category, Photo } from '@redux/types/response.type'
import { AppDispatch } from '@redux/store'
import { useDispatch } from 'react-redux'
import { getCategories } from '@redux/slices/category.slice'
import {
  CreateCategoryInput,
  UpdateCategoryInput,
} from '@redux/types/request.type'

export const useCategoryPhotos = (categoryId: string | undefined) => {
  return useQuery({
    queryKey: [`categoryPhotos${categoryId}`],
    queryFn: async () => {
      const { data } = await privateApi.get(`/categories/${categoryId}/photos`)
      return data.data as Photo[]
    },
  })
}

export const useCategory = (categoryId: string | undefined) => {
  return useQuery({
    queryKey: [categoryId],
    queryFn: async () => {
      const { data } = await privateApi.get(`/categories/${categoryId}`)
      return data.data as Category
    },
  })
}

export const useAddPhotoToCategory = (categoryId: string) => {
  const formData = new FormData()
  formData.append('categoryId', categoryId)
  const queryClient = useQueryClient()
  const dispatch = useDispatch<AppDispatch>()
  return useMutation({
    mutationKey: ['addPhotoToCategory', categoryId],
    mutationFn: async (photoId: string) => {
      formData.delete('photoId')
      formData.append('photoId', photoId)
      const { data } = await privateApi.post(`/photo-category`, formData)
      return data.data
    },
    onSuccess: () => {
      queryClient.invalidateQueries({
        queryKey: ['infinitePhotos'],
      })
      queryClient.invalidateQueries({
        queryKey: [`categoryPhotos${categoryId}`],
      })
      queryClient.invalidateQueries({
        queryKey: [`${categoryId}`],
      })
      queryClient.invalidateQueries({
        queryKey: [`infinitePhotosNotInCategory${categoryId}`],
      })
      dispatch(getCategories())
    },
  })
}
export const useRemovePhotoFromCategory = (categoryId: string) => {
  const formData = new FormData()
  formData.append('categoryId', categoryId)
  const queryClient = useQueryClient()
  const dispatch = useDispatch<AppDispatch>()
  return useMutation({
    mutationKey: ['removePhotoFromCategory', categoryId],
    mutationFn: async (photoId: string) => {
      formData.append('photoId', photoId)
      const { data } = await privateApi.post(`photo-category/delete`, formData)
      return data.data
    },
    onSuccess: () => {
      queryClient.invalidateQueries({
        queryKey: ['infinitePhotos'],
      })
      queryClient.invalidateQueries({
        queryKey: [`categoryPhotos${categoryId}`],
      })
      queryClient.invalidateQueries({
        queryKey: [`${categoryId}`],
      })
      queryClient.invalidateQueries({
        queryKey: [`infinitePhotosNotInCategory${categoryId}`],
      })
      dispatch(getCategories())
    },
  })
}

export const useCreateCategory = () => {
  const dispatch = useDispatch<AppDispatch>()
  return useMutation({
    mutationKey: ['createCategory'],
    mutationFn: async (input: CreateCategoryInput) => {
      const formData = new FormData()
      for (const [key, value] of Object.entries(input)) {
        formData.append(key, value)
      }
      const { data } = await privateApi.post(`/categories`, formData)
      return data.data as Category
    },
    onSuccess: () => {
      dispatch(getCategories())
    },
  })
}

export const useUpdateCategory = (categoryId: string) => {
  const queryClient = useQueryClient()
  const dispatch = useDispatch<AppDispatch>()
  return useMutation({
    mutationKey: ['updateCategory'],
    mutationFn: async (updateInput: UpdateCategoryInput) => {
      const formData = new FormData()
      for (const [key, value] of Object.entries(updateInput)) {
        formData.append(key, value)
      }
      const { data } = await privateApi.post(
        `/categories/${categoryId}`,
        formData,
      )
      return data.data as Category
    },
    onSuccess: () => {
      queryClient.invalidateQueries({
        queryKey: [`${categoryId}`],
      })
      dispatch(getCategories())
    },
  })
}

export const useDeleteCategory = (categoryId: string) => {
  const dispatch = useDispatch<AppDispatch>()
  return useMutation({
    mutationKey: ['deleteCategory'],
    mutationFn: async () => {
      const { data } = await privateApi.delete(`/categories/${categoryId}`)
      return data.data as number
    },
    onSuccess: () => {
      dispatch(getCategories())
    },
  })
}

export const useGetPhotosNotInCategoryByPage = (categoryId: string) => {
  const LIMIT = 20
  return useInfiniteQuery({
    queryKey: [`infinitePhotosNotInCategory${categoryId}`],
    queryFn: async ({ pageParam = 1 }) => {
      const { data } = await privateApi.get(
        `/categories/${categoryId}/photos/not-in-category/${pageParam}/${LIMIT}`,
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

export const useGetPhotosInCategoryByPage = (
  categoryId: string | undefined,
) => {
  const LIMIT = 20
  return useInfiniteQuery({
    queryKey: ['photosInCategory', categoryId],
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
