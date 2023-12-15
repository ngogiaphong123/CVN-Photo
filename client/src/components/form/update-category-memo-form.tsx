import * as z from 'zod'
import { useForm } from 'react-hook-form'
import { zodResolver } from '@hookform/resolvers/zod'
import { Form, FormControl, FormField, FormItem } from '@/components/ui/form'
import { Category } from '@redux/types/response.type'
import { useUpdateCategory } from '@/hooks/category.hook'
import { Textarea } from '@components/ui/textarea'
const formSchema = z.object({
  memo: z.string().optional(),
})
export default function UpdateCategoryMemoForm({
  category,
}: {
  category: Category
}) {
  const { mutateAsync: updateCategory } = useUpdateCategory(category.id)
  const form = useForm<z.infer<typeof formSchema>>({
    resolver: zodResolver(formSchema),
    defaultValues: {
      memo: category.memo,
    },
  })
  const onSubmit = async (values: z.infer<typeof formSchema>) => {
    if (values.memo === undefined) values.memo = ''
    await updateCategory({
      memo: values.memo,
    })
  }
  return (
    <Form {...form}>
      <form onSubmit={form.handleSubmit(onSubmit)}>
        <div className="flex items-center justify-start w-full gap-4">
          <div className="flex flex-col w-full">
            <FormField
              control={form.control}
              name="memo"
              render={({ field }) => (
                <FormItem>
                  <FormControl>
                    <Textarea
                      {...field}
                      placeholder={'Write a memo for this category (optional)'}
                      onKeyDown={e => {
                        if (e.key === 'Enter' && e.shiftKey === false) {
                          e.preventDefault()
                          e.stopPropagation()
                          form.handleSubmit(onSubmit)()
                        }
                      }}
                      className="w-full p-0 text-xl font-light border-0 shadow-none ring-0 focus-visible:border-primary focus-visible:ring-primary focus-visible:ring-offset-2 focus-visible:ring-offset-white text-muted-foreground"
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
