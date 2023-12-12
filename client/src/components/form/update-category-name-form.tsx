import * as z from 'zod'
import { useForm } from 'react-hook-form'
import { zodResolver } from '@hookform/resolvers/zod'
import { Form, FormControl, FormField, FormItem } from '@/components/ui/form'
import { Category } from '@/redux/types/response.type'
import { Input } from '@components/ui/input'
import { useUpdateCategory } from '@/hooks/category.hook'
const formSchema = z.object({
  name: z.string().min(3, {
    message: 'Name must be at least 3 characters long',
  }),
})
export default function UpdateCategoryNameForm({
  category,
}: {
  category: Category
}) {
  const { mutateAsync: updateCategory } = useUpdateCategory(category.id)

  const form = useForm<z.infer<typeof formSchema>>({
    resolver: zodResolver(formSchema),
    defaultValues: {
      name: category.name === 'favorite' ? 'Favorite' : category.name,
    },
  })
  const onSubmit = async (values: z.infer<typeof formSchema>) => {
    await updateCategory({
      name: values.name,
    })
  }
  return (
    <Form {...form}>
      <form onSubmit={form.handleSubmit(onSubmit)}>
        <div className="flex items-center justify-start w-full gap-4">
          <div className="flex flex-col w-full">
            <FormField
              control={form.control}
              name="name"
              render={({ field }) => (
                <FormItem>
                  <FormControl>
                    <Input
                      {...field}
                      placeholder="Category Name"
                      className="w-full p-0 text-3xl font-bold border-0 shadow-none ring-0 text-primary focus-visible:border-primary focus-visible:ring-primary focus-visible:ring-offset-2 focus-visible:ring-offset-white placeholder-primary"
                    />
                  </FormControl>
                </FormItem>
              )}
            />
          </div>
        </div>
      </form>
    </Form>
  )
}
